<?php

namespace App\Entity\Contracts;

interface Sluggable
{
	/**
	 * @param  null|string  $sluggable
	 * @return $this|Sluggable
	 */
	public function setSlug(?string $sluggable): Sluggable;
	
	/**
	 * @return null|string
	 */
	public function getSlug(): ?string;
}