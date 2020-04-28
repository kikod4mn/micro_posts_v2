<?php

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\CountsLikes;
use App\Entity\Concerns\CountsViews;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\HasTimestamps;
use App\Entity\Concerns\CanPublish;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\CountableLikes;
use App\Entity\Contracts\CountableViews;
use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\TimeStampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment extends AbstractEntity implements CountableViews, CountableLikes, Authorable, TimeStampable, Publishable
{
	use CountsLikes, CountsViews, HasAuthor, HasTimestamps, CanPublish;
	
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="bigint")
	 */
	protected $id;
	
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
	 * @return null|int
	 */
	public function getId(): ?int
	{
		return $this->id;
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
	
	/**
	 * @return bool
	 */
	public function isReported(): ?bool
	{
		return $this->reported;
	}
	
	/**
	 * Report a comment as inappropriate.
	 */
	public function report(): void
	{
		$this->reported = true;
		$this->reportCount++;
		// If more than 10 reports come in, hide the post from public
		if ($this->reportCount > 10) {
			$this->disApprove();
		}
	}
	
	/**
	 * Clear a posts reported status and set reportCount to 0.
	 */
	public function clearReported(): void
	{
		$this->reportCount = 0;
		$this->reported    = false;
	}
	
	/**
	 * @return int
	 */
	public function getReportCount(): ?int
	{
		return $this->reportCount;
	}
	
	/**
	 * @param  int  $reportCount
	 */
	public function setReportCount(int $reportCount): void
	{
		$this->reportCount = $reportCount;
	}
}
