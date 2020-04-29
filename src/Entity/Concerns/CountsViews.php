<?php

namespace App\Entity\Concerns;

use Doctrine\ORM\Mapping as ORM;

trait CountsViews
{
	/**
	 * @ORM\Column(type="bigint", nullable=false)
	 * @var int
	 */
	private $viewCount = 0;
	
	/**
	 * @ORM\Column(type="bigint", nullable=false)
	 * @var int
	 */
	private $weeklyViewCount = 0;
	
	/**
	 * @return null|int
	 */
	public function getViewCount(): ?int
	{
		return $this->viewCount;
	}
	
	/**
	 * @return null|int
	 */
	public function getWeeklyViewCount(): ?int
	{
		return $this->weeklyViewCount;
	}
	
	/**
	 * Increment all view counters.
	 * @return void
	 */
	public function incrementViewCounters(): void
	{
		$this->weeklyViewCount++;
		$this->viewCount++;
		
		$this->em->flush();
	}
	
	/**
	 * Reset the weekly view counter. Only recommend to have weekly counter reset, otherwise, why bother with all time?
	 * @return void
	 */
	public function resetWeeklyViewCount(): void
	{
		$this->weeklyViewCount = 0;
		
		$this->em->flush();
	}
}