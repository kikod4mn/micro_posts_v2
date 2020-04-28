<?php

namespace App\Event;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserMentionedInCommentEvent extends Event
{
	/**
	 * @var string
	 */
	public const NAME = 'comment.mention.user';
	
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * @var Comment
	 */
	private $comment;
	
	/**
	 * UserMentionedInCommentEvent constructor.
	 * @param  User     $user
	 * @param  Comment  $comment
	 */
	public function __construct(User $user, Comment $comment)
	{
		$this->user    = $user;
		$this->comment = $comment;
	}
	
	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}