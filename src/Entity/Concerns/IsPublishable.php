<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\Publishable;
use DateTime;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;

trait IsPublishable
{
	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var bool
	 */
	protected $published = false;
	
	/**
	 * @Groups({"default"})
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var DateTime
	 */
	protected $publishedAt;
	
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
	
	/**
	 * @param  DateTimeInterface  $publishedAt
	 * @return $this|Publishable
	 */
	public function setPublishedAt(DateTimeInterface $publishedAt): Publishable
	{
		$this->publishedAt = $publishedAt;
		
		return $this;
	}
	
	/**
	 * @return null|DateTimeInterface
	 */
	public function getPublishedAt(): ?DateTimeInterface
	{
		return $this->publishedAt;
	}
}