<?php

declare(strict_types = 1);

namespace App\Entity\Concerns\UserConcerns;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait HasMicroPosts
{
	/**
	 * @Groups({"administer", "user-with-posts"})
	 * @ORM\OneToMany(targetEntity="App\Entity\MicroPost", mappedBy="author", cascade={"all"})
	 * @var Collection
	 */
	protected $microPosts;
	
	/**
	 * @Groups({"administer", "user-with-posts"})
	 * @ORM\ManyToMany(targetEntity="App\Entity\MicroPost", mappedBy="likedBy", cascade={"all"})
	 * @var Collection
	 */
	protected $microPostsLiked;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\MicroPost", mappedBy="reportedBy", cascade={"all"})
	 * @var Collection
	 */
	protected $reportedMicroPosts;
	
	/**
	 * @Groups({"administer", "user-with-comments"})
	 * @ORM\OneToMany(targetEntity="App\Entity\MicroComment", mappedBy="author", cascade={"all"})
	 * @var Collection
	 */
	protected $microComments;
	
	/**
	 * @Groups({"administer", "user-with-comments"})
	 * @ORM\ManyToMany(targetEntity="App\Entity\MicroComment", mappedBy="likedBy", cascade={"all"})
	 * @var Collection
	 */
	protected $microCommentsLiked;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\MicroComment", mappedBy="reportedBy", cascade={"all"})
	 * @var Collection
	 */
	protected $reportedMicroComments;
	
	/**
	 * @return null|Collection
	 */
	public function getMicroPosts(): ?Collection
	{
		return $this->microPosts;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getMicroPostsLiked(): ?Collection
	{
		return $this->microPostsLiked;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getReportedMicroPosts(): ?Collection
	{
		return $this->reportedMicroPosts;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getMicroComments(): ?Collection
	{
		return $this->microComments;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getMicroCommentsLiked(): ?Collection
	{
		return $this->microCommentsLiked;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getReportedMicroComments(): ?Collection
	{
		return $this->reportedMicroComments;
	}
}