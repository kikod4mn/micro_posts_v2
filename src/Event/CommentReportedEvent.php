<?php

namespace App\Event;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class CommentReportedEvent extends Event
{
	/**
	 * @var string
	 */
	public const NAME = 'comment.reported';
	
	/**
	 * @var Comment
	 */
	private $comment;
	
	/**
	 * @var User
	 */
	private $moderator;
	
	/**
	 * CommentReportedEvent constructor.
	 * @param  User     $moderator
	 * @param  Comment  $comment
	 */
	public function __construct(User $moderator, Comment $comment)
	{
		$this->moderator = $moderator;
		$this->comment   = $comment;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
	
	/**
	 * @return User
	 */
	public function getModerator(): User
	{
		return $this->moderator;
	}
	
	/**
	 * @param  User  $moderator
	 */
	public function setModerator(User $moderator): void
	{
		$this->moderator = $moderator;
	}
}