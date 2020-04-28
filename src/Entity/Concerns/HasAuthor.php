<?php

namespace App\Entity\Concerns;

use App\Entity\Contracts\Authorable;
use App\Entity\User;

trait HasAuthor
{
	/**
	 * @return null|Authorable
	 */
	public function getAuthor()
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