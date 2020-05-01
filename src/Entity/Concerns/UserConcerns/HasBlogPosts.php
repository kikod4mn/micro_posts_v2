<?php

declare(strict_types = 1);

namespace App\Entity\Concerns\UserConcerns;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait HasBlogPosts
{
	/**
	 * @Groups({"administer", "user-with-posts"})
	 * @ORM\OneToMany(targetEntity="App\Entity\BlogPost", mappedBy="author", cascade={"all"})
	 * @var Collection
	 */
	protected $blogPosts;
	
	/**
	 * @Groups({"administer", "user-with-posts"})
	 * @ORM\ManyToMany(targetEntity="App\Entity\BlogPost", mappedBy="likedBy", cascade={"all"})
	 * @var Collection
	 */
	protected $blogPostsLiked;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\BlogPost", mappedBy="reportedBy", cascade={"all"})
	 * @var Collection
	 */
	protected $reportedBlogPosts;
	
	/**
	 * @Groups({"administer", "user-with-comments"})
	 * @ORM\OneToMany(targetEntity="App\Entity\BlogComment", mappedBy="author", cascade={"all"})
	 * @var Collection
	 */
	protected $blogComments;
	
	/**
	 * @Groups({"administer", "user-with-comments"})
	 * @ORM\ManyToMany(targetEntity="App\Entity\BlogComment", mappedBy="likedBy", cascade={"all"})
	 * @var Collection
	 */
	protected $blogCommentsLiked;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\BlogComment", mappedBy="reportedBy", cascade={"all"})
	 * @var Collection
	 */
	protected $reportedBlogComments;
	
	/**
	 * @return null|Collection
	 */
	public function getBlogPosts(): ?Collection
	{
		return $this->blogPosts;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getBlogPostsLiked(): ?Collection
	{
		return $this->blogPostsLiked;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getReportedBlogPosts(): ?Collection
	{
		return $this->reportedBlogPosts;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getBlogComments(): ?Collection
	{
		return $this->blogComments;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getBlogCommentsLiked(): ?Collection
	{
		return $this->blogCommentsLiked;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getReportedBlogComments(): ?Collection
	{
		return $this->reportedBlogComments;
	}
}