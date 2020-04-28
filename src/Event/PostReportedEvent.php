<?php

namespace App\Event;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class PostReportedEvent extends Event
{
	/**
	 * @var string
	 */
	public const NAME = 'post.reported';
	
	/**
	 * @var Post
	 */
	private $post;
	
	/**
	 * @var User
	 */
	private $moderator;
	
	/**
	 * PostReportedEvent constructor.
	 * @param  User  $moderator
	 * @param  Post  $post
	 */
	public function __construct(User $moderator, Post $post)
	{
		$this->moderator = $moderator;
		$this->post      = $post;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
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