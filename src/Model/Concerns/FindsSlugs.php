<?php

declare(strict_types = 1);

namespace App\Model\Concerns;

use App\Entity\Contracts\Trashable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait FindsSlugs
{
	/**
	 * @param  string  $slug
	 * @return null|mixed
	 */
	public function findSlug(string $slug)
	{
		return $this
			->result($this->repository->findOneBy(['id' => $slug]))
			->return()
			;
	}
	
	/**
	 * @param  array  $slugs
	 * @return mixed|mixed[]
	 */
	public function findManyBySlugs(array $slugs)
	{
		return $this
			->result($this->repository->findby(['slug' => $slugs]))
			->return()
			;
	}
}