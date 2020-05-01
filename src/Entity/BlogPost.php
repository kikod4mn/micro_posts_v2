<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\IsLikable;
use App\Entity\Concerns\IsPublishable;
use App\Entity\Concerns\IsReportable;
use App\Entity\Concerns\IsTrashable;
use App\Entity\Concerns\CountsLikes;
use App\Entity\Concerns\CountsViews;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\HasSlug;
use App\Entity\Concerns\HasTimestamps;
use App\Entity\Concerns\HasUuid;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\CountableLikes;
use App\Entity\Contracts\CountableViews;
use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\Reportable;
use App\Entity\Contracts\Sluggable;
use App\Entity\Contracts\TimeStampable;
use App\Entity\Contracts\Trashable;
use App\Entity\Contracts\Uniqable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="`blog_post`")
 * @UniqueEntity(fields="uuid", message="How did this happen???? Uuid should be unique!!")
 * @UniqueEntity(fields="slug", message="How did this happen???? Slug should be unique!!")
 * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
 */
class BlogPost extends AbstractEntity
	implements CountableLikes, CountableViews, TimeStampable, Authorable, Publishable, Reportable, Trashable, Uniqable, Sluggable
{
	use HasUuid, CountsViews, CountsLikes, HasAuthor, HasTimestamps, IsPublishable, IsReportable, IsTrashable, HasSlug, IsLikable;
	
	/**
	 * @var string
	 */
	const SLUGGABLE_FIELD = 'title';
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 * @var string
	 */
	protected $title;
	
	/**
	 * @ORM\Column(type="text", nullable=false)
	 * @Assert\NotBlank()
	 */
	protected $body;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="blogPosts")
	 * @ORM\JoinColumn(nullable=false)
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var Authorable|User|Collection
	 */
	protected $author;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Picture", mappedBy="blogPost")
	 * @ORM\JoinColumn(nullable=false)
	 * @var Picture|Collection
	 */
	protected $headerImage;
	
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\Gallery", inversedBy="blogPost")
	 * @var Gallery|Collection
	 */
	protected $gallery;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\BlogComment", mappedBy="blogPost", cascade={"all"})
	 * @var BlogComment[]|Collection
	 */
	protected $comments;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="blogPostsLiked", cascade={"all"})
	 * @ORM\JoinTable(name="blog_post_likes",
	 *     joinColumns={
	 *          @ORM\JoinColumn(name="blog_post_id", referencedColumnName="id", nullable=false)
	 *     },
	 *     inverseJoinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 *     }
	 * )
	 * @var User[]|Collection
	 */
	protected $likedBy;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="reportedBlogPosts")
	 * @ORM\JoinTable(name="reported_blog_posts", joinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 *      },
	 *      inverseJoinColumns={
	 *          @ORM\JoinColumn(name="blog_post_id", referencedColumnName="id", nullable=false)
	 *      }
	 * )
	 * @var User[]|Collection
	 */
	protected $reportedBy;
	
	/**
	 * Post constructor.
	 */
	public function __construct()
	{
		$this->author      = new ArrayCollection();
		$this->headerImage = new ArrayCollection();
		$this->gallery     = new ArrayCollection();
		$this->comments    = new ArrayCollection();
		$this->likedBy     = new ArrayCollection();
		$this->reportedBy  = new ArrayCollection();
	}
	
	/**
	 * @return string
	 */
	public function getBody(): ?string
	{
		return $this->body;
	}
	
	/**
	 * @param  string  $body
	 * @return BlogPost
	 */
	public function setBody(string $body): self
	{
		$this->body = $this->cleanString($body);
		
		return $this;
	}
	
	/**
	 * @return null|Collection|BlogComment[]
	 */
	public function getComments(): ?Collection
	{
		return $this->comments;
	}
	
	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}
	
	/**
	 * @param  string  $title
	 */
	public function setTitle(string $title): void
	{
		$this->title = $title;
	}
}
