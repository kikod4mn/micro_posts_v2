<?php

namespace App\Entity\Contracts;

use App\Entity\User;

interface Authorable
{
	/**
	 * @return null|Authorable
	 */
	public function getAuthor();
	
	/**
	 * @param  User  $author
	 * @return $this|Authorable
	 */
	public function setAuthor(User $author): Authorable;
}