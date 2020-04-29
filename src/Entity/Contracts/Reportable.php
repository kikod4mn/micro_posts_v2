<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;

interface Reportable
{
	/**
	 * @return null|bool
	 */
	public function isReported(): ?bool;
	
	/**
	 * @param  User  $user
	 * @return $this|Reportable
	 */
	public function report(User $user): Reportable;
	
	/**
	 * @return $this|Reportable
	 */
	public function clearReportedStatus(): Reportable;
	
	/**
	 * @return null|int
	 */
	public function reportCount(): ?int;
	
	/**
	 * @return null|Collection
	 */
	public function getReportedBy(): ?Collection;
}