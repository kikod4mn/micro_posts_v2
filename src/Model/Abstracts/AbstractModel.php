<?php

declare(strict_types = 1);

namespace App\Model\Abstracts;

use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\Trashable;
use App\Model\Concerns\CreatesAlias;
use App\Model\Contracts\Filterable;
use App\Model\Contracts\Paginatable;
use App\Support\Contracts\FilterInterface;
use App\Support\Contracts\Jsonable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

abstract class AbstractModel
{
	use CreatesAlias;
	
	/**
	 * @var ServiceEntityRepository
	 */
	protected $repository;
	
	/**
	 * @var string
	 */
	protected $alias;
	
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
	 * @var bool
	 */
	protected $asMany;
	
	/**
	 * Provide filtering of collections after database retrieval. Recommended to filter results before for better performance and more stable pagination.
	 * Use as a failsafe for not showing unwanted results to the public.
	 * @var FilterInterface
	 */
	protected $filter;
	
	/**
	 * @var QueryBuilder
	 */
	private $qb;
	
	/**
	 * @var bool
	 */
	private $trashed = false;
	
	/**
	 * @var bool
	 */
	private $unPublished = false;
	
	/**
	 * AbstractModel constructor.
	 * @param  ServiceEntityRepository  $repository
	 * @param  null|FilterInterface     $filter  Can pass in null if the model will not require a filtering process.
	 */
	public function __construct(ServiceEntityRepository $repository, ?FilterInterface $filter)
	{
		$this->repository = $repository;
		
		$this->createAliases();
		
		// Add default filters if the entity implements Filterable.
		// Default filters are ['Publishable', 'Trashable']
		if ($this instanceof Filterable && ! is_null($filter)) {
			
			$this->filter = $filter;
			
			$this->filters();
		}
	}
	
	/**
	 * @return null|ServiceEntityRepository'
	 */
	public function getRepository(): ?ServiceEntityRepository
	{
		return $this->repository;
	}
	
	/**
	 * @param  ServiceEntityRepository  $repository
	 * @return $this
	 */
	public function setRepository(ServiceEntityRepository $repository): self
	{
		$this->repository = $repository;
		
		return $this;
	}
	
	/**
	 * Get the query builder alias for the model.
	 * @return string
	 */
	public function getAlias(): string
	{
		return $this->alias;
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
		return $this
			->result($this->repository->findOneBy(['id' => $id]))
			->return()
			;
	}
	
	/**
	 * @param  array       $criteria
	 * @param  null|array  $orderBy
	 * @param  int|null    $limit
	 * @param  int|null    $offset
	 * @return mixed|mixed[]
	 */
	public function findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
	{
		//		var_dump($this->formatQuery($criteria, $orderBy, $limit, $offset)->getQuery()->getSQL());
		//		die;
		
		return $this->formatQuery($criteria, $orderBy, $limit, $offset)->getQuery()->getResult();
		
		return $this
			->result($this->formatQuery($criteria, $orderBy, $limit, $offset)->getQuery()->getResult())
			->return()
			;
	}
	
	/**
	 * @param  array       $ids
	 * @param  array|null  $orderBy
	 * @param  int|null    $limit
	 * @param  int|null    $offset
	 * @return mixed[]
	 */
	public function findMany(array $ids, array $orderBy = null, int $limit = null, int $offset = null)
	{
		return $this->findBy($ids, $orderBy, $limit, $offset);
	}
	
	/**
	 * @return mixed[]
	 */
	public function all()
	{
		var_dump($this->formatQuery([])->getQuery()->getSQL());
		
		return $this->formatQuery([])->getQuery()->getResult();
		
		return $this
			->result($this->defaultQuery([])->getQuery()->getResult())
			->return()
			;
	}
	
	/**
	 * @param  array       $criteria
	 * @param  null|array  $orderBy
	 * @param  null|int    $limit
	 * @param  null|int    $offset
	 * @return QueryBuilder
	 */
	protected function formatQuery(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): QueryBuilder
	{
		$this->qb = $this->getQb()->select($this->getAlias());
		
		if (! empty($criteria)) {
			$this->setCriteria($criteria);
		}
		
		if (! empty($orderBy)) {
			$this->setOrderBy($orderBy);
		}
		
		$this->offset($offset);
		
		$this->limit($limit);
		
		if ($this->_e_publishable) {
			$this->includeUnpublished();
		}
		
		if ($this->_e_trashable) {
			$this->includeTrashed();
		}
		
		$qb = $this->qb;
		
		// Reset query builder.
		$this->qb = null;
		
		return $qb;
	}
	
	/**
	 * todo - support QB\Expr class as possible values
	 * Set simple key value criteria.
	 * @param  array  $criteria
	 * @return $this
	 */
	private function setCriteria(array $criteria): self
	{
		foreach ($criteria as $column => $criterion) {
			
			if (is_array($criterion)) {
				// If we are dealing with multiple options for the same column, loop and set them all for the same column.
				foreach ($criterion as $subCriterion) {
					$this->searchCriterion($column, $subCriterion);
				}
			} else {
				var_dump($column);
				$this->searchCriterion($column, $criterion);
			}
		}
		
		return $this;
	}
	
	/**
	 * Set a single key, value pair and parameter with a slightly randomised placeholder.
	 * @param  string           $column
	 * @param  string|int|bool  $criterion
	 */
	private function searchCriterion(string $column, $criterion): void
	{
		// Generate randomized placeholder name.
		$placeholder = $column . rand(0, 100);
		
		$this->qb->orWhere(sprintf('%s.%s = :%s', $this->getAlias(), $column, $placeholder));
		$this->qb->setParameter($placeholder, $criterion);
	}
	
	/**
	 * todo - support QB\Expr class as possible values
	 * Set all orderBy criteria for the QB.
	 * @param  array  $orderBy
	 */
	private function setOrderBy(array $orderBy): void
	{
		foreach ($orderBy as $sort => $order) {
			$this->orderCriterion($sort, $order);
		}
	}
	
	/**
	 * Set a single orderBy on the QB.
	 * @param        $sort
	 * @param  null  $order
	 */
	private function orderCriterion($sort, $order = null)
	{
		$this->qb->addOrderBy($this->getAlias() . '.' . $sort, $order);
	}
	
	/**
	 * Determine if there is an offset passed in. If we have an offset, that will be returned and nothing more done.
	 * If the model is set for pagination and implements the marker interface, we check if we have an offset and return if we do.
	 * If all else fails, work with null!
	 * @param  null|int  $offset
	 */
	private function offset(int $offset = null): void
	{
		$offset = ! is_null($offset)
			? $offset
			: ($this instanceof Paginatable && $this->isPaginated()
				? $this->hasOffset()
				: null);
		
		if (null === $offset) return;
		
		$this->qb->setFirstResult($offset);
	}
	
	/**
	 * Determine if there is a limit passed in. If we have a limit, that will be returned and nothing more done.
	 * If the model is set for pagination and implements the marker interface, we check if we have a limit and return if we do.
	 * If all else fails, work with null!
	 * @param  null|int  $limit
	 */
	private function limit(int $limit = null): void
	{
		$limit = ! is_null($limit)
			? $limit
			: ($this instanceof Paginatable && $this->isPaginated()
				? $this->hasLimit()
				: null);
		
		if (null === $limit) return;
		
		$this->qb->setMaxResults($limit);
	}
	
	/**
	 * Determine whether to include the un-published results.
	 * Run "onlyUnPublished()" to receive only the un-published entries.
	 */
	private function includeUnpublished(): void
	{
		if (! $this->withUnPublished()) {
			$this->qb->andWhere(sprintf("%s.publishedAt IS NOT NULL", $this->getAlias()));
		} else {
			$this->qb->andWhere(sprintf("%s.publishedAt IS NULL", $this->getAlias()));
		}
	}
	
	/**
	 * Determine whether to include the trashed results.
	 * Run "onlyTrashed()" to receive only the trashed entries.
	 */
	private function includeTrashed(): void
	{
		if (! $this->withTrashed()) {
			$this->qb->andWhere(sprintf("%s.trashedAt IS NULL", $this->getAlias()));
		} else {
			$this->qb->andWhere(sprintf("%s.trashedAt IS NOT NULL", $this->getAlias()));
		}
	}
	
	public function withTrashed(): bool
	{
		return $this->trashed;
	}
	
	public function onlyTrashed()
	{
		$this->trashed = true;
		
		return $this;
	}
	
	public function withUnPublished(): bool
	{
		return $this->unPublished;
	}
	
	public function onlyUnPublished()
	{
		$this->unPublished = true;
		
		return $this;
	}
	
	/**
	 * Build the default query with pagination, trashable and publishable capability.
	 * @param  array       $criteria
	 * @param  null|array  $orderBy
	 * @param  null|int    $limit
	 * @param  null|int    $offset
	 * @return QueryBuilder
	 */
	protected function defaultQuery(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): QueryBuilder
	{
		$qb = $this->getQb()->select($this->getAlias());
		
		if (! empty($criteria)) {
			foreach ($criteria as $key => $value) {
				// We can pass in for instance an array of ids which we wish all to be found.
				if (is_array($value)) {
					foreach ($value as $item) {
						$qb->orWhere(sprintf('%s = :%s', (string) $item, (string) $key));
					}
				} else {
					$qb->orWhere(sprintf('%s = :%s', (string) $value, (string) $key));
				}
			}
		}
		
		if ($this->entity instanceof Trashable) {
			$qb->andWhere(sprintf("%s.trashedAt IS NULL", $this->getAlias()));
		}
		
		if ($this->entity instanceof Publishable) {
			$qb->andWhere(sprintf("%s.publishedAt IS NOT NULL", $this->getAlias()));
		}
		
		if ($limit) {
			$qb->setMaxResults($limit);
		}
		
		if ($offset) {
			$qb->setFirstResult($offset);
		}
		
		return $qb;
	}
	
	/**
	 * todo - factor in current user and admin separation
	 * Return all trashed entities.
	 * @return array|string|void
	 */
	public function getTrashed()
	{
		return $this
			->result(
				$this
					->getQb()
					->select($this->alias)
					->where(sprintf("%s.trashedAt IS NOT NULL", $this->alias))
					->orderBy(sprintf("%s.createdAt", $this->alias), 'desc')
					->getQuery()
					->getResult()
				, false
			)
			->return()
			;
	}
	
	/**
	 * todo - factor in current user and admin separation
	 * Return all trashed entities.
	 * @return array|string|void
	 */
	public function getUnpublished()
	{
		return $this
			->result(
				$this
					->getQb()
					->select($this->alias)
					->where(sprintf("%s.trashedAt IS NOT NULL", $this->alias))
					->orderBy(sprintf("%s.createdAt", $this->alias), 'desc')
					->getQuery()
					->getResult()
				, false
			)
			->return()
			;
	}
	
	/**
	 * Set the result.
	 * @param  object|array  $result
	 * @param  bool          $enableFilters  False for queries not to be filtered.
	 * @return $this|AbstractModel
	 */
	protected function result($result, bool $enableFilters = true): self
	{
		$this->result = $result;
		
		// Check if we have an array as result from Doctrine.
		$this->asMany = $this->isMany();
		
		if ($this instanceof Filterable) {
			$this->result = $this->filter->filter($this->result);
		}
		
		return $this;
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
		return ! $this->hasStringKeys($this->result) && is_array($this->result);
	}
	
	/**
	 * @param  array  $array
	 * @return bool
	 */
	protected function hasStringKeys(array $array)
	{
		foreach (array_keys($array) as $key) {
			if (is_string($key)) {
				
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Return the result array as a pure result or format it for json.
	 * @return null|array|string
	 */
	protected function asMany()
	{
		return $this->asJson ? $this->returnManyAsJson() : $this->result;
	}
	
	/**
	 * Return the result as a pure result or format it for json.
	 * @return null|array|string
	 */
	protected function asOne()
	{
		return $this->asJson ? $this->returnOneAsJson() : $this->result;
	}
	
	/**
	 * Attempt to use the "toJson" method if implemented.
	 * If not, try a simple "json_encode" and see what comes out.
	 * @return string
	 */
	protected function returnOneAsJson(): string
	{
		if ($this->result instanceof Jsonable) {
			
			return $this->result->toJson($this->groups);
		}
		
		return (string) json_encode($this->result);
	}
	
	/**
	 * Loop over the result array, attempt to use "toJson" on the entity.
	 * If not implemented, attempt conversion to array and json encoding.
	 * @return string
	 */
	protected function returnManyAsJson(): string
	{
		$return = [];
		
		foreach ($this->result as $datum) {
			if ($datum instanceof Jsonable) {
				$return[] = $datum->toArray($this->groups);
			} else {
				try {
					return (string) json_encode((array) $datum);
				} catch (Throwable $e) {
					return 'null';
				}
			}
		}
		
		return (string) json_encode($return);
	}
	
	/**
	 * @return QueryBuilder
	 */
	protected function getQb(): QueryBuilder
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
	
	/**
	 * Set all filters for this model.
	 */
	private function filters(): void
	{
		$this->filter->addFilter(
			'publishable', function ($entity) {
			if ($entity instanceof Publishable) {
				
				if (! $entity->isPublished()) {
					
					return null;
				}
			}
			
			return $entity;
		}
		);
		
		$this->filter->addFilter(
			'trashable', function ($entity) {
			if ($entity instanceof Trashable) {
				
				if ($entity->isTrashed()) {
					
					return null;
				}
			}
			
			return $entity;
		}
		);
	}
}