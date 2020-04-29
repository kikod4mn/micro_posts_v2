<?php

namespace App\Model\Abstracts;

use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\Trashable;
use App\Model\Concerns\DecodesUuid;
use App\Support\Concerns\WorksClassname;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractModel
{
	use WorksClassname, DecodesUuid;
	
	/**
	 * @var ServiceEntityRepository
	 */
	private $repository;
	
	/**
	 * @var bool
	 */
	private $withTrashed = null;
	
	/**
	 * @var string
	 */
	private $alias;
	
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
	 * @param  int  $id
	 * @return null|mixed
	 */
	public function find(int $id)
	{
		$result = $this->repository->findOneBy(['id' => $id]);
		
		if ($result instanceof Trashable && $result->isTrashed()) {
			
			throw new NotFoundHttpException();
		}
		
		return $result;
	}
	
	/**
	 * If parameter not found, throws exception.
	 * @param  int  $id
	 * @return mixed
	 */
	public function findOrFail(int $id)
	{
		$result = $this->repository->findOneBy(['id' => $id]);
		
		if ($result instanceof Trashable && $result->isTrashed() || null === $result) {
			
			throw new NotFoundHttpException();
		}
		
		return $result;
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
	 * @param  array  $ids
	 * @return mixed[]
	 */
	public function findMany(array $ids)
	{
		return $this->findBy(['id' => $ids]);
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
	 * Filter out trashed and un-published results.
	 * @param  array  $results
	 * @return array
	 */
	protected function filter(array $results): array
	{
		return $this->filterPublished(
			$this->filterTrashed(
				$results
			)
		);
	}
	
	/**
	 * @param  array  $results
	 * @return array
	 */
	protected function filterTrashed(array $results): array
	{
		for ($i = 0; $i < count($results); $i++) {
			
			if ($results[$i] instanceof Trashable && $results[$i]->isTrashed()) {
				
				array_splice($results, $i, 1);
			}
		}
		
		return $results;
	}
	
	/**
	 * @param  array  $results
	 * @return array
	 */
	public function filterPublished(array $results): array
	{
		for ($i = 0; $i < count($results); $i++) {
			
			if ($results[$i] instanceof Publishable && ! $results[$i]->isPublished()) {
				
				array_splice($results, $i, 1);
			}
		}
		
		return $results;
	}
	
	/**
	 * @return QueryBuilder
	 */
	protected function getQB(): QueryBuilder
	{
		return $this->repository->createQueryBuilder($this->alias);
	}
}