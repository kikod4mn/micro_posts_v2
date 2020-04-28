<?php

namespace App\Entity\Concerns;

use App\Entity\Contracts\TimeStampable;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints\Time;

trait HasTimestamps
{
	/**
	 * @ORM\Column(type="datetime")
	 * @var DateTime
	 */
	private $createdAt;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var DateTime
	 */
	private $updatedAt;
	
	/**
	 * Set usage of timestamps on the entity.
	 * @var bool
	 */
	protected $timestamps = true;
	
	/**
	 * @return string
	 */
	public function getCreatedAtColumn(): string
	{
		return defined('static::CREATED_AT') ? static::CREATED_AT : 'createdAt';
	}
	
	/**
	 * @return string
	 */
	public function getUpdatedAtColumn(): string
	{
		return defined('static::UPDATED_AT') ? static::UPDATED_AT : 'updatedAt';
	}
	
	/**
	 * @return DateTimeInterface
	 */
	public function getCreatedAt(): ?DateTimeInterface
	{
		return $this->{$this->getCreatedAtColumn()};
	}
	
	/**
	 * @param  DateTimeInterface  $createdAt
	 * @return $this|TimeStampable
	 */
	public function setCreatedAt(DateTimeInterface $createdAt): TimeStampable
	{
		$this->{$this->getCreatedAtColumn()} = $createdAt;
		
		return $this;
	}
	
	/**
	 * @return DateTimeInterface
	 */
	public function getUpdatedAt(): ?DateTimeInterface
	{
		return $this->{$this->getUpdatedAtColumn()};
	}
	
	/**
	 * @param  DateTimeInterface  $updatedAt
	 * @return $this|TimeStampable
	 */
	public function setUpdatedAt(DateTimeInterface $updatedAt): TimeStampable
	{
		$this->{$this->getUpdatedAtColumn()} = $updatedAt;
		
		return $this;
	}
	
	/**
	 * @return $this|TimeStampable
	 */
	public function setCreationTimestamps(): TimeStampable
	{
		$this->setCreatedAt($this->freshTimestamp());
		
		$this->setUpdatedAt($this->freshTimestamp());
		
		return $this;
	}
	
	/**
	 * @return $this|TimeStampable
	 */
	public function setUpdatedTimestamps(): TimeStampable
	{
		$this->setUpdatedAt($this->freshTimestamp());
		
		return $this;
	}
	
	/**
	 * @param  DateTimeInterface  $dateTime
	 * @return string
	 */
	protected function serializeDate(DateTimeInterface $dateTime): string
	{
		return Carbon::instance($dateTime)->toJSON();
	}
	
	/**
	 * Determine if the entity is using timestamps.
	 * @return bool
	 */
	protected function hasTimestamps(): bool
	{
		return $this->timestamps;
	}
	
	/**
	 * getCreatedAt and getUpdatedAt are already included by default.
	 * @return array
	 */
	protected function getDates()
	{
		$defaults = [
			static::CREATED_AT,
			static::UPDATED_AT,
		];
		
		return $this->hasTimestamps()
			? array_merge($this->dates, $defaults)
			: $this->dates;
	}
	
	/**
	 * @return DateTimeInterface
	 */
	protected function freshTimestamp(): DateTimeInterface
	{
		return new DateTime('now');
	}
}