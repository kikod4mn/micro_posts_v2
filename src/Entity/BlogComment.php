<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Contracts\Authorable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="`blog_comment`")
 * @UniqueEntity(fields="uuid", message="How did this happen???? Uuid should be unique!!")
 * @UniqueEntity(fields="slug", message="How did this happen???? Slug should be unique!!")
 * @ORM\Entity(repositoryClass="App\Repository\BlogCommentRepository")
 */
class BlogComment extends BaseComment
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost", inversedBy="comments")
	 * @ORM\JoinColumn(nullable=false)
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var BlogPost
	 */
	protected $blogPost;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="blogComments", fetch="EAGER")
	 * @ORM\JoinColumn(nullable=false)
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var Authorable|User
	 */
	protected $author;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="blogCommentsLiked")
	 * @ORM\JoinTable(name="blog_comment_likes",
	 *     joinColumns={
	 *          @ORM\JoinColumn(name="comment_id", referencedColumnName="id", nullable=false)
	 *     },
	 *     inverseJoinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 *     }
	 * )
	 * @var User[]|Collection
	 */
	protected $likedBy;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="reportedBlogComments")
	 * @ORM\JoinTable(name="reported_blog_comments", joinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 *      },
	 *      inverseJoinColumns={
	 *          @ORM\JoinColumn(name="comment_id", referencedColumnName="id", nullable=false)
	 *      }
	 * )
	 * @var User[]|Collection
	 */
	protected $reportedBy;
	
	/**
	 * Blog Post Comment constructor.
	 */
	public function __construct()
	{
		$this->likedBy    = new ArrayCollection();
		$this->blogPost   = new ArrayCollection();
		$this->reportedBy = new ArrayCollection();
	}
	
	/**
	 * @return null|Collection|BlogPost
	 */
	public function getBlogPost(): ?Collection
	{
		return $this->blogPost;
	}
	
	/**
	 * @param  BlogPost  $blogPost
	 * @return BlogComment
	 */
	public function setBlogPost(BlogPost $blogPost): self
	{
		$this->blogPost = $blogPost;
		
		return $this;
	}
}