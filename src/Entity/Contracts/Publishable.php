<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

interface Publishable
{
	public function isPublished(): ?bool;
	
	public function publish(): Publishable;
	
	public function unPublish(): Publishable;
}