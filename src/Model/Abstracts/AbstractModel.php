<?php

declare(strict_types = 1);

namespace App\Model\Abstracts;

use App\Entity\Contracts\Trashable;
use App\Support\Concerns\WorksClassname;
use App\Support\Contracts\Jsonable;
use App\Support\Str;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

abstract class AbstractModel
{
	use WorksClassname;
	
	/**
	 * @var ServiceEntityRepository
	 */
	protected $repository;
	
	/**
	 * Set the query to include trashed items.
	 * @var bool
	 */
	protected $withTrashed = null;
	
	/**
	 * Set the query result to return as json string.
	 * @var bool
	 */
	protected $asJson = false;
	
	/**
	 * Normalization context groups.
	 * @var array
	 */
	protected $groups = [];
	
	/**
	 * Results from the query.
	 * @var array
	 */
	protected $result = null;
	
	/**
	 * @var string
	 */
	protected $alias;
	
	/**
	 * Set filters according to interfaces.
	 * Make sure all methods and interfaces are set on the Entity.
	 * EXAMPLE - ['publishable:published', 'trashable:trashed', 'interface_name:method_name']
	 * Filter interface namespace is expected to be "App\Entity\Contracts".
	 * Filter method will be tried with "is", "has", "get" and without any prefix.
	 * Result is expected as boolean. True will show the entity, false will not.
	 * @var string[]
	 */
	protected $filters = [];
	
	/**
	 * @var array
	 */
	private $validFilters;
	
	/**
	 * @var bool
	 */
	protected $asMany;
	
	/**
	 * AbstractModel constructor.
	 * @param  ServiceEntityRepository  $repository
	 */
	public function __construct(ServiceEntityRepository $repository)
	{
		$this->repository = $repository;
		$this->alias      = $this->getAliasForQB();
	}
	
	/**
	 * @return null|ServiceEntityRepository'
	 */
	protected function getRepository(): ?ServiceEntityRepository
	{
		return $this->repository;
	}
	
	/**
	 * @param  ServiceEntityRepository  $repository
	 * @return $this
	 */
	protected function setRepository(ServiceEntityRepository $repository): self
	{
		$this->repository = $repository;
		
		return $this;
	}
	
	/**
	 * Set the return type on the model as json.
	 * @return $this
	 */
	public function asJson(): self
	{
		$this->asJson = true;
		
		return $this;
	}
	
	/**
	 * Set normalization context groups.
	 * @param  array  $groups
	 * @return $this
	 */
	public function groups(array $groups): self
	{
		$this->groups = $groups;
		
		return $this;
	}
	
	/**
	 * @param  int  $id
	 * @return null|mixed
	 */
	public function find(int $id)
	{
		$result = $this->repository->findOneBy(['id' => $id]);
		
		return $this->return();
	}
	
	/**
	 * @param  int[]  $ids
	 * @return mixed[]
	 */
	public function findMany(array $ids)
	{
		return $this->findBy(['id' => $ids]);
	}
	
	/**
	 * @param  array       $criteria
	 * @param  null|array  $orderBy
	 * @param  null|int    $limit
	 * @param  null|int    $offset
	 * @return mixed|mixed[]
	 */
	public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
	{
		return $this->filter(
			$this->repository->findBy($criteria, $orderBy, $limit, $offset)
		);
	}
	
	/**
	 * @return mixed[]
	 */
	public function all()
	{
		return $this->filter(
			$this->repository->findBy([])
		);
	}
	
	/**
	 * todo - factor in current user and admin separation
	 * Return all trashed entities.
	 * @return QueryBuilder
	 */
	public function getTrashed()
	{
		$qb = $this->getQB();
		
		return $qb
			->select($this->alias)
			->where(sprintf("%s.trashedAt IS NOT NULL", $this->alias))
			->orderBy(sprintf("%s.createdAt", $this->alias), 'desc')
			->getQuery()
			->getResult()
			;
	}
	
	/**
	 * todo - factor in current user and admin separation
	 * Return all trashed entities.
	 * @return QueryBuilder
	 */
	public function getUnpublished()
	{
		$qb = $this->getQB();
		
		return $qb
			->select($this->alias)
			->where(sprintf("%s.trashedAt IS NOT NULL", $this->alias))
			->orderBy(sprintf("%s.createdAt", $this->alias), 'desc')
			->getQuery()
			->getResult()
			;
	}
	
	/**
	 * Set the result.
	 * @param  object|array  $result
	 * @return $this|AbstractModel
	 */
	protected function result($result): self
	{
		$this->result = $result;
		
		return $this;
	}
	
	/**
	 * Process all valid filters on the model.
	 * If no filters exist, you can skip this method.
	 * Alternatively you can specify the filters as a parameter to this method.
	 * @param  string[]  $filters
	 * @return $this|AbstractModel
	 */
	protected function filters(array $filters = []): self
	{
		if (! empty($filters)) {
			
			$this->filters = $filters;
		}
		
		foreach ($this->filters as $name) {
			$this->getFilter($name);
		}
		
		foreach ($this->validFilters as $filter) {
		
		}
		
		return $this;
	}
	
	protected function getFilter($name): void
	{
		$interface = 'App\Entity\Contracts\\' . Str::studly(Str::before($name, ':'));
		$method    = Str::after($name, ':');
		
		if ($this->interfaceExists($interface, $method)) {
			$this->validFilters[$interface] = $method;
		}
	}
	
	/**
	 * @param  string  $interface
	 * @param  string  $method
	 * @return null|string
	 */
	protected function interfaceExists(string $interface, string $method): ?string
	{
		$prefixes = ['is', 'get', 'has', ''];
		
		if (interface_exists($interface)) {
			
			foreach ($prefixes as $prefix) {
				if (method_exists($interface, $prefix . Str::studly($method))) {
					
					return $prefix . Str::studly($method);
				}
			}
		}
		
		return null;
	}
	
	protected function filter(string $method): void
	{
	
	}
	
	/**
	 * Return the processed result(s).
	 * @return void|string|array
	 */
	protected function return()
	{
		if (is_null($this->result) || empty($this->result)) {
			
			$this->throw404();
		}
		
		return $this->asMany
			? $this->asMany()
			: $this->asOne();
	}
	
	/**
	 * Check if Doctrine result is an array of many results or a single.
	 * @return bool
	 */
	protected function isMany(): bool
	{
		return $this->asMany = is_array($this->result) ? true : false;
	}
	
	protected function asMany()
	{
		// todo write filtration here
		return $this->asJson ? $this->returnManyAsJson($this->result, $this->groups) : $this->result;
	}
	
	protected function asOne()
	{
		return $this->asJson ? $this->returnOneAsJson($this->result, $this->groups) : $this->result;
	}
	
	/**
	 * @return $this|AbstractModel
	 */
	protected function removeTrashed(): self
	{
		for ($i = 0; $i < count($this->result); $i++) {
			
			if ($this->result[$i]->isTrashed()) {
				
				$this->removeResult($i);
			}
		}
		
		return $this;
	}
	
	/**
	 * @return $this|AbstractModel
	 */
	protected function removeUnPublished(): self
	{
		for ($i = 0; $i < count($this->result); $i++) {
			
			if (! $this->result[$i]->isPublished()) {
				
				$this->removeResult($i);
			}
		}
		
		return $this;
	}
	
	protected function removeResult($offset): void
	{
		array_splice($this->result, $offset, 1);
	}
	
	/**
	 * Attempt to use the "toJson" method if implemented.
	 * If not, try a simple "json_encode" and see what comes out.
	 * @param  object|mixed  $object
	 * @param  array         $groups
	 * @return string
	 */
	protected function returnOneAsJson($object, array $groups): string
	{
		if ($object instanceof Jsonable) {
			
			return $object->toJson($groups);
		}
		
		return (string) json_encode($object, 0, 1);
	}
	
	/**
	 * @param  array|Collection  $data
	 * @param  array             $groups
	 * @return string
	 */
	protected function returnManyAsJson($data, array $groups): string
	{
		$return = [];
		
		foreach ($data as $datum) {
			if ($datum instanceof Jsonable) {
				$return[] = $datum->toArray($groups);
			} else {
				try {
					$datum = (array) $datum;
				} catch (Throwable $e) {
					$datum = null;
				}
			}
		}
		
		return (string) json_encode($return, 0, 1);
	}
	
	/**
	 * @return QueryBuilder
	 */
	protected function getQB(): QueryBuilder
	{
		return $this->repository->createQueryBuilder($this->alias);
	}
	
	/**
	 * Throw a 404 Not Fount Exception.
	 * @throws NotFoundHttpException
	 */
	protected function throw404(): void
	{
		throw new NotFoundHttpException();
	}
}