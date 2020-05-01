<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

interface Publishable
{
	/**
	 * @return null|bool
	 */
	public function isPublished(): ?bool;
	
	/**
	 * @return Publishable
	 */
	public function publish(): Publishable;
	
	/**
	 * @return Publishable
	 */
	public function unPublish(): Publishable;
}