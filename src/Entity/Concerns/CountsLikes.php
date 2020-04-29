<?php

namespace App\Entity\Concerns;

trait CountsLikes
{
	/**
	 * @ORM\Column(type="bigint", nullable=false)
	 * @var int
	 */
	private $likeCount = 0;
	
	/**
	 * @ORM\Column(type="bigint", nullable=false)
	 * @var int
	 */
	private $weeklyLikeCount = 0;
	
	/**
	 * @return null|int
	 */
	public function getLikeCount(): ?int
	{
		return $this->likeCount;
	}
	
	/**
	 * @return null|int
	 */
	public function getWeeklyLikeCount(): ?int
	{
		return $this->weeklyLikeCount;
	}
	
	/**
	 * Increment the like counters.
	 * @return void
	 */
	public function incrementLikeCounters(): void
	{
		$this->likeCount++;
		$this->weeklyLikeCount++;
		
		$this->em->flush();
	}
	
	/**
	 * Reset only the weekly counter. No point in gathering all time count if we reset it weekly!
	 * @return void
	 */
	public function resetWeeklyLikeCounter(): void
	{
		$this->weeklyLikeCount = 0;
		
		$this->em->flush();
	}
}