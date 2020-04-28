<?php

namespace App\Event;

use App\Entity\Comment;
use Symfony\Contracts\EventDispatcher\Event;

class CommentEditedEvent extends Event
{
	/**
	 * @var string
	 */
	public const NAME = 'comment.edited';
	
	/**
	 * @var Comment
	 */
	private $comment;
	
	/**
	 * CommentCreatedEvent constructor.
	 * @param  Comment  $comment
	 */
	public function __construct(Comment $comment)
	{
		$this->comment = $comment;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
}