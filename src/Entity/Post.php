<?php

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\CanReport;
use App\Entity\Concerns\CountsLikes;
use App\Entity\Concerns\CountsViews;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\HasTimestamps;
use App\Entity\Concerns\CanPublish;
use App\Entity\Concerns\CanTrash;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\CountableLikes;
use App\Entity\Contracts\CountableViews;
use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\Reportable;
use App\Entity\Contracts\TimeStampable;
use App\Entity\Contracts\Trashable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post extends AbstractEntity implements CountableLikes, CountableViews, TimeStampable, Authorable, Publishable, Reportable, Trashable
{
	use CountsViews, CountsLikes, HasAuthor, HasTimestamps, CanPublish, CanReport, CanTrash;
	
	/**
	 * @Groups({"default"})
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="bigint")
	 */
	protected $id;
	
	/**
	 * @Groups({"default"})
	 * @ORM\Column(type="string", length=2024)
	 * @Assert\NotBlank()
	 * @Assert\Length(min="10", max="280", minMessage="Please enter a minimum of 10 characters!", maxMessage="No more than 280 characters allowed!")
	 */
	protected $body;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
	 * @ORM\JoinColumn(nullable=false)
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var Authorable
	 */
	protected $author;
	
	/**
	 * @Groups({"default", "user-with-posts"})
	 * @ORM\ManyToMany(targetEntity="User", inversedBy="postsLiked", cascade={"all"})
	 * @ORM\JoinTable(name="post_likes",
	 *     joinColumns={
	 *          @ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=false)
	 *     },
	 *     inverseJoinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 *     }
	 * )
	 * @var Collection
	 */
	protected $likedBy;
	
	/**
	 * @Groups({"default", "user-with-posts"})
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", cascade={"all"})
	 * @var Collection
	 */
	protected $comments;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="reportedPosts")
	 * @ORM\JoinTable(name="reported_posts", joinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 *      },
	 *      inverseJoinColumns={
	 *          @ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=false)
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
		$this->likedBy  = new ArrayCollection();
		$this->comments = new ArrayCollection();
	}
	
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
		$this->body = $body;
	}
	
	/**
	 * @return Collection
	 */
	public function getComments()
	{
		return $this->comments;
	}
	
	/**
	 * @param  Comment  $comment
	 */
	public function addComment(Comment $comment): void
	{
		$this->comments->add($comment);
	}
	
	/**
	 * @return Collection
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
