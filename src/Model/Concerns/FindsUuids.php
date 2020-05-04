<?php

declare(strict_types = 1);

namespace App\Model\Concerns;

use App\Entity\Contracts\Trashable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait FindsUuids
{
	/**
	 * This method user RAW UUID for finding,
	 * for automatic decoding of encoded uuid's see methods with "Decode" in the name.
	 * @param  string  $uuid
	 * @return null|mixed
	 */
	public function findUuid(string $uuid)
	{
		return $this
			->result($this->repository->findOneBy(['uuid' => $uuid]))
			->return()
			;
	}
	
	/**
	 * This method user RAW UUID for finding,
	 * for automatic decoding of encoded uuid's see methods with "Decode" in the name.
	 * @param  string[]  $uuids
	 * @return mixed[]
	 */
	public function findManyUuid(array $uuids)
	{
		return $this
			->result($this->repository->findBy(['uuid' => $uuids]))
			->return()
			;
	}
	
	/**
	 * This method expects an encoded uuid that it will first decode, and then use that to find the entity.
	 * @param  string  $uuid
	 * @return null|mixed
	 */
	public function findUuidDecode(string $uuid)
	{
		return $this
			->result($this->repository->findOneBy(['uuid' => $this->decode($uuid)]))
			->return()
			;
	}
	
	/**
	 * This method expects an encoded uuid that it will first decode, and then use that to find the entity.
	 * @param  string[]  $uuids
	 * @return mixed[]
	 */
	public function findManyUuidDecode(array $uuids)
	{
		$decoded = [];
		
		foreach ($uuids as $uuid) {
			$decoded[] = $this->decode($uuid);
		}
		
		return
			$this->result($this->findBy(['uuid' => $decoded]))
			     ->return()
			;
	}
}