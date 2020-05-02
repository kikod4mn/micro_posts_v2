<?php

declare(strict_types = 1);

namespace App\Model\Concerns;

use App\Entity\Contracts\Trashable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait FindsBySlug
{
	/**
	 * @param  string  $slug
	 * @return null|mixed
	 */
	public function findSlug(string $slug)
	{
		$result = $this->repository->findOneBy(['slug' => $slug]);
		
		if ($result instanceof Trashable && $result->isTrashed()) {
			
			throw new NotFoundHttpException();
		}
		
		return $result;
	}
	
	/**
	 * This method user RAW UUID for finding,
	 * for automatic decoding of encoded uuid's see methods with "Decode" in the name.
	 * If parameter not found, throws exception.
	 * @param  string  $slug
	 * @return mixed
	 */
	public function findOrFailSlug(string $slug)
	{
		$result = $this->repository->findOneBy(['slug' => $slug]);
		
		if ($result instanceof Trashable && $result->isTrashed() || null === $result) {
			
			throw new NotFoundHttpException();
		}
		
		return $result;
	}
	
	/**
	 * @param  array  $slugs
	 * @return mixed|mixed[]
	 */
	public function findManyBySlugs(array $slugs)
	{
		return $this->findBy(['slug' => $slugs]);
	}
}