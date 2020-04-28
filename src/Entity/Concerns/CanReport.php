<?php

namespace App\Entity\Concerns;

use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\Reportable;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait requires a field of reportedBy on the entity with a many to many relationship to the user entity.
 */
trait CanReport
{
	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var bool
	 */
	protected $reported = false;
	
	/**
	 * @ORM\Column(type="integer", nullable=false)
	 * @var int
	 */
	protected $reportCount = 0;
	
	/**
	 * @return null|bool
	 */
	public function isReported(): ?bool
	{
		return $this->reported;
	}
	
	/**
	 * Report a Reportable entity as inappropriate.
	 * This method also calls the EntityManager to save the entity to the database.
	 * Use this instead of addReported
	 * @param  User  $user
	 * @return $this|Reportable
	 */
	public function report(User $user): Reportable
	{
		$this->reported = true;
		$this->reportCount++;
		$this->addReport($user);
		
		// If too many reports come in, un-publish the entity if implements Publishable.
		if (! $this instanceof Publishable) {
			
			return $this;
		}
		
		// Default is 20 unique reports this is set as a const on the implementing class.
		if ($this->reportCount > $this->getMaxCount()) {
			
			$this->unPublish();
		}
		
		$this->em->flush();
		
		return $this;
	}
	
	/**
	 * Clear a posts reported status and set reportCount to 0.
	 * @return $this|Reportable
	 */
	public function clearReportedStatus(): Reportable
	{
		$this->reportCount = 0;
		$this->reported    = false;
		$this->reportedBy  = new ArrayCollection();
		
		return $this;
	}
	
	/**
	 * @return null|int
	 */
	public function reportCount(): ?int
	{
		return $this->reportCount;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getReportedBy(): ?Collection
	{
		return $this->reportedBy;
	}
	
	/**
	 * @param  User  $user
	 * @return $this|Reportable
	 */
	protected function addReport(User $user): Reportable
	{
		// Guard check for init
		$this->init();
		
		// To generate unique reports, make sure the user is not already in the collection.
		if (! $this->reportedBy->contains($user)) {
			
			$this->reportedBy->add($user);
		}
		
		return $this;
	}
	
	/**
	 * If creating a new Entity, reported would need to be initialized in the constructor.
	 * Since we are including in a trait, we define an init method to check and init the reportedBy property.
	 */
	protected function init(): void
	{
		if (! $this->isInitialized()) {
			
			$this->reportedBy = new ArrayCollection();
		}
	}
	
	/**
	 * Check if reportedBy is initialized
	 * @return bool
	 */
	protected function isInitialized(): bool
	{
		return ! is_null($this->reportedBy);
	}
	
	/**
	 * @return bool
	 */
	protected function getMaxCount(): bool
	{
		return defined('static::MAX_REPORTS') ? static::MAX_REPORTS : 20;
	}
}