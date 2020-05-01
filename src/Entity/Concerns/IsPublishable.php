<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\Publishable;

trait IsPublishable
{
	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var bool
	 */
	protected $published = false;
	
	/**
	 * @return bool
	 */
	public function isPublished(): ?bool
	{
		return $this->published;
	}
	
	/**
	 * Publish an entity.
	 * @return $this|Publishable
	 */
	public function publish(): Publishable
	{
		$this->published = true;
		
		return $this;
	}
	
	/**
	 * Un-publish an entity.
	 * @return $this|Publishable
	 */
	public function unPublish(): Publishable
	{
		$this->published = false;
		
		return $this;
	}
}