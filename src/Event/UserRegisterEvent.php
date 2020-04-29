<?php

declare(strict_types = 1);

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisterEvent extends Event
{
	/**
	 * @var string
	 */
	const NAME = "user.register";
	
	/**
	 * @var User
	 */
	private $registeredUser;
	
	/**
	 * UserRegisterEvent constructor.
	 * @param  User  $user
	 */
	public function __construct(User $user)
	{
		$this->registeredUser = $user;
	}
	
	/**
	 * @return User
	 */
	public function getRegisteredUser(): User
	{
		return $this->registeredUser;
	}
}