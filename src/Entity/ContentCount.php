<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Concerns\HasTimestamps;
use App\Entity\Contracts\TimeStampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="content_count")
 * @ORM\Entity(repositoryClass="App\Repository\ContentCountsRepository")
 */
final class ContentCount implements TimeStampable
{
	use HasTimestamps;
	
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="bigint", nullable=true)
	 * @var int
	 */
	protected $publicMicroPostCount;
	
	/**
	 * @ORM\Column(type="bigint", nullable=true)
	 * @var int
	 */
	protected $publicMicroCommentCount;
	
	/**
	 * @ORM\Column(type="bigint", nullable=true)
	 * @var int
	 */
	protected $publicBlogPostCount;
	
	/**
	 * @ORM\Column(type="bigint", nullable=true)
	 * @var int
	 */
	protected $publicBlogCommentCount;
	
	/**
	 * @ORM\Column(type="bigint", nullable=true)
	 * @var int
	 */
	protected $publicPictureCount;
	
	/**
	 * @ORM\Column(type="bigint", nullable=true)
	 * @var int
	 */
	protected $publicGalleryCount;
	
	/**
	 * @return null|mixed
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
	/**
	 * @return null|int
	 */
	public function getPublicMicroPostCount(): ?int
	{
		return $this->publicMicroPostCount;
	}
	
	/**
	 * @param  int  $publicMicroPostCount
	 * @return $this|ContentCount
	 */
	public function setPublicMicroPostCount(int $publicMicroPostCount): ContentCount
	{
		$this->publicMicroPostCount = $publicMicroPostCount;
		
		return $this;
	}
	
	/**
	 * @return null|int
	 */
	public function getPublicMicroCommentCount(): ?int
	{
		return $this->publicMicroCommentCount;
	}
	
	/**
	 * @param  int  $publicMicroCommentCount
	 * @return $this|ContentCount
	 */
	public function setPublicMicroCommentCount(int $publicMicroCommentCount): ContentCount
	{
		$this->publicMicroCommentCount = $publicMicroCommentCount;
		
		return $this;
	}
	
	/**
	 * @return null|int
	 */
	public function getPublicBlogPostCount(): ?int
	{
		return $this->publicBlogPostCount;
	}
	
	/**
	 * @param  int  $publicBlogPostCount
	 * @return $this|ContentCount
	 */
	public function setPublicBlogPostCount(int $publicBlogPostCount): ContentCount
	{
		$this->publicBlogPostCount = $publicBlogPostCount;
		
		return $this;
	}
	
	/**
	 * @return null|int
	 */
	public function getPublicBlogCommentCount(): ?int
	{
		return $this->publicBlogCommentCount;
	}
	
	/**
	 * @param  int  $publicBlogCommentCount
	 * @return $this|ContentCount
	 */
	public function setPublicBlogCommentCount(int $publicBlogCommentCount): ContentCount
	{
		$this->publicBlogCommentCount = $publicBlogCommentCount;
		
		return $this;
	}
	
	/**
	 * @return null|int
	 */
	public function getPublicPictureCount(): ?int
	{
		return $this->publicPictureCount;
	}
	
	/**
	 * @param  int  $publicPictureCount
	 * @return $this|ContentCount
	 */
	public function setPublicPictureCount(int $publicPictureCount): ContentCount
	{
		$this->publicPictureCount = $publicPictureCount;
		
		return $this;
	}
	
	/**
	 * @return null|int
	 */
	public function getPublicGalleryCount(): ?int
	{
		return $this->publicGalleryCount;
	}
	
	/**
	 * @param  int  $publicGalleryCount
	 * @return $this|ContentCount
	 */
	public function setPublicGalleryCount(int $publicGalleryCount): ContentCount
	{
		$this->publicGalleryCount = $publicGalleryCount;
		
		return $this;
	}
}
