<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
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
	use HasUuid, CountsViews, CountsLikes, HasAuthor, HasTimestamps, IsPublishable, IsReportable, IsTrashable, HasSlug;
	
	/**
	 * @var string
	 */
	const SLUGGABLE_FIELD = 'title';
	
	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank()
	 * @var string
	 */
	protected $title;
	
	/**
	 * @Groups({"default"})
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank()
	 */
	protected $body;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="blogPosts")
	 * @ORM\JoinColumn(nullable=false)
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var Authorable
	 */
	protected $author;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Picture", mappedBy="blogPost")
	 * @var Collection
	 */
	protected $headerImage;
	
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\Gallery", mappedBy="blogPost")
	 * @var Collection
	 */
	protected $gallery;
	
	/**
	 * @Groups({"default", "user-with-posts"})
	 * @ORM\OneToMany(targetEntity="App\Entity\BlogComment", mappedBy="blogPost", cascade={"all"})
	 * @var Collection
	 */
	protected $comments;
	
	/**
	 * @Groups({"default", "user-with-posts"})
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="blogPostsLiked", cascade={"all"})
	 * @ORM\JoinTable(name="blog_post_likes",
	 *     joinColumns={
	 *          @ORM\JoinColumn(name="blog_post_id", referencedColumnName="id", nullable=false)
	 *     },
	 *     inverseJoinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 *     }
	 * )
	 * @var Collection
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
	 * @var Collection
	 */
	protected $reportedBy;
	
	/**
	 * Post constructor.
	 */
	public function __construct()
	{
		$this->likedBy    = new ArrayCollection();
		$this->reportedBy = new ArrayCollection();
		$this->comments   = new ArrayCollection();
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
	 * @return null|Collection
	 */
	public function getComments(): ?Collection
	{
		return $this->comments;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getLikedBy(): ?Collection
	{
		return $this->likedBy;
	}
	
	/**
	 * Like a post if not already liked.
	 * @param  UserInterface  $user
	 */
	public function like(UserInterface $user): void
	{
		if (! $this->likedBy->contains($user)) {
			$this->likedBy->add($user);
		}
	}
	
	/**
	 * Dislike a post if previously liked.
	 * @param  UserInterface  $user
	 */
	public function unlike(UserInterface $user): void
	{
		if ($this->likedBy->contains($user)) {
			$this->likedBy->removeElement($user);
		}
	}
	
	/**
	 * Helper to get quick like count for a comment.
	 * @return int
	 */
	public function likesCount(): ?int
	{
		return $this->likedBy->count();
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
