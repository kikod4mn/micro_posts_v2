<?php

namespace App\Event;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class FollowedUserCreatesAPostEvent extends Event
{
	/**
	 * @var string
	 */
	public const NAME = 'followed.user.creates.post';
	
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * @var Post
	 */
	private $post;
	
	/**
	 * FollowedUserCreatesAPost constructor.
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
}