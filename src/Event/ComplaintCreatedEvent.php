<?php

namespace App\Event;

use App\Entity\Complaint;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ComplaintCreatedEvent extends Event
{
	/**
	 * @var string
	 */
	public const NAME = 'complaint.created';
	
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * @var Complaint
	 */
	private $complaint;
	
	/**
	 * @var User
	 */
	private $postedBy;
	
	/**
	 * ComplaintCreatedEvent constructor.
	 * @param  User       $user
	 * @param  Complaint  $complaint
	 * @param  User       $postedBy
	 */
	public function __construct(User $user, Complaint $complaint, User $postedBy)
	{
		$this->user      = $user;
		$this->complaint = $complaint;
		$this->postedBy  = $postedBy;
	}
	
	/**
	 * @return User
	 */
	public function getPostedBy(): User
	{
		return $this->postedBy;
	}
	
	/**
	 * @param  User  $postedBy
	 */
	public function setPostedBy(User $postedBy): void
	{
		$this->postedBy = $postedBy;
	}
	
	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}
	
	/**
	 * @param  User  $user
	 */
	public function setUser(User $user): void
	{
		$this->user = $user;
	}
	
	/**
	 * @return Complaint
	 */
	public function getComplaint(): Complaint
	{
		return $this->complaint;
	}
	
	/**
	 * @param  Complaint  $complaint
	 */
	public function setComplaint(Complaint $complaint): void
	{
		$this->complaint = $complaint;
	}
}