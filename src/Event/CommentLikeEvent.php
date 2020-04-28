<?php

namespace App\Event;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class CommentLikeEvent extends Event
{
	/**
	 * @var string
	 */
	public const NAME = 'comment.liked';
	
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * @var Post
	 */
	private $post;
	
	/**
	 * @var Comment
	 */
	private $comment;
	
	/**
	 * CommentLikeEvent constructor.
	 * @param  User     $user
	 * @param  Post     $post
	 * @param  Comment  $comment
	 */
	public function __construct(User $user, Post $post, Comment $comment)
	{
		$this->user    = $user;
		$this->post    = $post;
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
	 * @param  User  $user
	 */
	public function setUser(User $user): void
	{
		$this->user = $user;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
	
	/**
	 * @param  Post  $post
	 */
	public function setPost(Post $post): void
	{
		$this->post = $post;
	}
	
	/**
	 * @return Comment
	 */
	public function getComment(): Comment
	{
		return $this->comment;
	}
	
	/**
	 * @param  Comment  $comment
	 */
	public function setComment(Comment $comment): void
	{
		$this->comment = $comment;
	}
}