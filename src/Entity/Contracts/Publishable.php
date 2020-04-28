<?php

namespace App\Entity\Contracts;

interface Publishable
{
	public function isPublished(): ?bool;
	
	public function publish(): Publishable;
	
	public function unPublish(): Publishable;
}