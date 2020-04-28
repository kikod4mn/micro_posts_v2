<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class StartFollowingUserEvent extends Event
{
	/**
	 * @var string
	 */
	public const NAME = 'start.following.user';
	
	/**
	 * @var User
	 */
	private $userBeingFollowed;
	
	/**
	 * @var User
	 */
	private $userDoingTheFollowing;
	
	/**
	 * @return User
	 */
	public function getUserBeingFollowed(): User
	{
		return $this->userBeingFollowed;
	}
	
	/**
	 * @param  User  $userBeingFollowed
	 */
	public function setUserBeingFollowed(User $userBeingFollowed): void
	{
		$this->userBeingFollowed = $userBeingFollowed;
	}
	
	/**
	 * @return User
	 */
	public function getUserDoingTheFollowing(): User
	{
		return $this->userDoingTheFollowing;
	}
	
	/**
	 * @param  User  $userDoingTheFollowing
	 */
	public function setUserDoingTheFollowing(User $userDoingTheFollowing): void
	{
		$this->userDoingTheFollowing = $userDoingTheFollowing;
	}
	
	/**
	 * StartFollowingUserEvent constructor.
	 * @param  User  $userBeingFollowed
	 * @param  User  $userDoingTheFollowing
	 */
	public function __construct(User $userBeingFollowed, User $userDoingTheFollowing)
	{
		$this->userBeingFollowed     = $userBeingFollowed;
		$this->userDoingTheFollowing = $userDoingTheFollowing;
	}
}