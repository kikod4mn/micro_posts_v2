<?php

namespace App\Event;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class PostLikeEvent extends Event
{
	/**
	 * @var string
	 */
	public const NAME = 'post.liked';
	
	/**
	 * @var Post
	 */
	private $post;
	
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * PostLikeEvent constructor.
	 * @param  User  $user
	 * @param  Post  $post
	 */
	public function __construct(User $user, Post $post)
	{
		$this->user = $user;
		$this->post = $post;
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
}