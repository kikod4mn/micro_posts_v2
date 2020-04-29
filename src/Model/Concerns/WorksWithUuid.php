<?php

declare(strict_types = 1);

namespace App\Model\Concerns;

use App\Entity\Contracts\Trashable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait WorksWithUuid
{
	/**
	 * This method user RAW UUID for finding,
	 * for automatic decoding of encoded uuid's see methods with "Decode" in the name.
	 * @param  string  $uuid
	 * @return null|mixed
	 */
	public function findUuid(string $uuid)
	{
		$result = $this->repository->findOneBy(['uuid' => $uuid]);
		
		if ($result instanceof Trashable && $result->isTrashed()) {
			
			throw new NotFoundHttpException();
		}
		
		return $result;
	}
	
	/**
	 * This method user RAW UUID for finding,
	 * for automatic decoding of encoded uuid's see methods with "Decode" in the name.
	 * If parameter not found, throws exception.
	 * @param  string  $uuid
	 * @return mixed
	 */
	public function findOrFailUuid(string $uuid)
	{
		$result = $this->repository->findOneBy(['uuid' => $uuid]);
		
		if ($result instanceof Trashable && $result->isTrashed() || null === $result) {
			
			throw new NotFoundHttpException();
		}
		
		return $result;
	}
	
	/**
	 * This method user RAW UUID for finding,
	 * for automatic decoding of encoded uuid's see methods with "Decode" in the name.
	 * @param  string[]  $uuids
	 * @return mixed[]
	 */
	public function findManyUuid(array $uuids)
	{
		return $this->findBy(['uuid' => $uuids]);
	}
	
	/**
	 * This method expects a decoded uuid that it will first decode, and then use that to find the entity.
	 * @param  string  $uuid
	 * @return null|mixed
	 */
	public function findUuidDecode(string $uuid)
	{
		$result = $this->repository->findOneBy(['uuid' => $uuid]);
		
		if ($result instanceof Trashable && $result->isTrashed()) {
			
			throw new NotFoundHttpException();
		}
		
		return $result;
	}
	
	/**
	 * This method expects a decoded uuid that it will first decode, and then use that to find the entity.
	 * If parameter not found, throws exception.
	 * @param  string  $uuid
	 * @return mixed
	 */
	public function findOrFailUuidDecode(string $uuid)
	{
		$result = $this->repository->findOneBy(['uuid' => $uuid]);
		
		if ($result instanceof Trashable && $result->isTrashed() || null === $result) {
			
			throw new NotFoundHttpException();
		}
		
		return $result;
	}
	
	/**
	 * This method expects a decoded uuid that it will first decode, and then use that to find the entity.
	 * @param  string[]  $uuids
	 * @return mixed[]
	 */
	public function findManyUuidDecode(array $uuids)
	{
		return $this->findBy(['uuid' => $uuids]);
	}
}