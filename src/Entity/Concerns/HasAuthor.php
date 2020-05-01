<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\Authorable;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;

trait HasAuthor
{
	/**
	 * @return null|Authorable|User|Collection
	 */
	public function getAuthor(): ?Authorable
	{
		return $this->author;
	}
	
	/**
	 * @param  User  $author
	 * @return $this|Authorable
	 */
	public function setAuthor(User $author): Authorable
	{
		$this->author = $author;
		
		return $this;
	}
}