<?php

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\CanReport;
use App\Entity\Concerns\CountsLikes;
use App\Entity\Concerns\CountsViews;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\HasSlug;
use App\Entity\Concerns\HasTimestamps;
use App\Entity\Concerns\CanPublish;
use App\Entity\Concerns\HasUuid;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\CountableLikes;
use App\Entity\Contracts\CountableViews;
use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\Reportable;
use App\Entity\Contracts\Sluggable;
use App\Entity\Contracts\TimeStampable;
use App\Entity\Contracts\Uniqable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="`comment`")
 * @UniqueEntity(fields="uuid", message="How did this happen???? Uuid should be unique!!")
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment extends AbstractEntity implements CountableViews, CountableLikes, Authorable, TimeStampable, Publishable, Reportable, Uniqable, Sluggable
{
	use HasUuid, CountsLikes, CountsViews, HasAuthor, HasTimestamps, CanPublish, CanReport, HasSlug;
	
	/**
	 * @var string
	 */
	const SLUGGABLE_FIELD = 'body';
	
	/**
	 * @ORM\Column(type="string", length=2024)
	 * @Assert\NotBlank()
	 * @Assert\Length(min="10", max="280", minMessage="Please enter a minimum of 10 characters!", maxMessage="No more than 280 characters allowed!")
	 */
	protected $body;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
	 * @ORM\JoinColumn(nullable=false)
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var Authorable
	 */
	protected $author;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="comments")
	 * @ORM\JoinTable(name="post_comments",
	 *     joinColumns={
	 *         @ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=false)
	 *     },
	 *     inverseJoinColumns={
	 *         @ORM\JoinColumn(name="comment_id", referencedColumnName="id", nullable=false)
	 *     }
	 * )
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var Collection
	 */
	protected $post;
	
	/**
	 * @ORM\ManyToMany(targetEntity="User", inversedBy="commentsLiked")
	 * @ORM\JoinTable(name="comment_likes",
	 *     joinColumns={
	 *          @ORM\JoinColumn(name="comment_id", referencedColumnName="id", nullable=false)
	 *     },
	 *     inverseJoinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 *     }
	 * )
	 * @var Collection
	 */
	protected $likedBy;
	
	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var bool
	 */
	protected $reported = false;
	
	/**
	 * @ORM\Column(type="integer", nullable=false)
	 * @var int
	 */
	protected $reportCount = 0;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="reportedComments")
	 * @ORM\JoinTable(name="reported_comments", joinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 *      },
	 *      inverseJoinColumns={
	 *          @ORM\JoinColumn(name="comment_id", referencedColumnName="id", nullable=false)
	 *      }
	 * )
	 * @var Collection
	 */
	protected $reportedBy;
	
	/**
	 * Comment constructor.
	 */
	public function __construct()
	{
		$this->likedBy = new ArrayCollection();
		$this->post    = new ArrayCollection();
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
	 */
	public function setBody(string $body): void
	{
		$this->body = $this->cleanString($body);
	}
	
	/**
	 * @return null|Collection
	 */
	public function getPost()
	{
		return $this->post;
	}
	
	/**
	 * @param  Post  $post
	 */
	public function setPost($post): void
	{
		$this->post = $post;
	}
	
	/**
	 * @return null|Collection
	 */
	public function getLikedBy()
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
}
