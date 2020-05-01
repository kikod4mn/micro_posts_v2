<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;

interface Authorable
{
	/**
	 * @return null|Authorable|User|Collection
	 */
	public function getAuthor(): ?Authorable;
	
	/**
	 * @param  User  $author
	 * @return $this|Authorable
	 */
	public function setAuthor(User $author): Authorable;
}