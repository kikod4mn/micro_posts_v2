<?php

namespace App\Event;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserMentionedInPostEvent extends Event
{
	/**
	 * @var string
	 */
	public const NAME = 'post.mention.user';
	
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * @var Post
	 */
	private $post;
	
	/**
	 * UserMentionedInPostEvent constructor.
	 * @param  User  $user
	 * @param  Post  $post
	 */
	public function __construct(User $user, Post $post)
	{
		$this->user = $user;
		$this->post = $post;
	}
	
	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}