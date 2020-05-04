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
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var DateTime
	 */
	protected $publishedAt;
	
	/**
	 * @return bool
	 */
	public function isPublished(): ?bool
	{
		return ! is_null($this->{$this->getPublishedAtColumn()});
	}
	
	/**
	 * @return null|DateTimeInterface
	 */
	public function getPublishedAt(): ?DateTimeInterface
	{
		return $this->{$this->getPublishedAtColumn()}();
	}
	
	/**
	 * @param  null|DateTimeInterface  $publishedAt
	 * @return $this|Publishable
	 */
	public function setPublishedAt(?DateTimeInterface $publishedAt): Publishable
	{
		$this->{$this->getPublishedAtColumn()} = $publishedAt;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getPublishedAtColumn(): ?string
	{
		return defined('static::PUBLISHED_AT') ? static::PUBLISHED_AT : 'publishedAt';
	}
	
	/**
	 * Publish an entity.
	 * @return $this|Publishable
	 */
	public function publish(): Publishable
	{
		$this->setPublishedAt($this->freshTimestamp());
		
		$this->em->flush();
		
		return $this;
	}
	
	/**
	 * Un-publish an entity.
	 * @return $this|Publishable
	 */
	public function unPublish(): Publishable
	{
		$this->setPublishedAt(null);
		
		$this->em->flush();
		
		return $this;
	}
}