<?php

namespace App\Event;

use App\Entity\Post;
use Symfony\Contracts\EventDispatcher\Event;

class PostEditedEvent extends Event
{
	/**
	 * @var string
	 */
	public const NAME = 'post.edited';
	
	/**
	 * @var Post
	 */
	private $post;
	
	/**
	 * NewPostCreatedEvent constructor.
	 * @param  Post  $post
	 */
	public function __construct(Post $post)
	{
		$this->post = $post;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
}