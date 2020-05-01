<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\IsReportable;
use App\Entity\Concerns\CountsLikes;
use App\Entity\Concerns\CountsViews;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\HasSlug;
use App\Entity\Concerns\HasTimestamps;
use App\Entity\Concerns\IsPublishable;
use App\Entity\Concerns\IsTrashable;
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
 * @ORM\Table(name="`micro_post`")
 * @UniqueEntity(fields="uuid", message="How did this happen???? Uuid should be unique!!")
 * @UniqueEntity(fields="slug", message="How did this happen???? Slug should be unique!!")
 * @ORM\Entity(repositoryClass="App\Repository\MicroPostRepository")
 */
class MicroPost extends AbstractEntity
	implements CountableLikes, CountableViews, TimeStampable, Authorable, Publishable, Reportable, Trashable, Uniqable, Sluggable
{
	use HasUuid, CountsViews, CountsLikes, HasAuthor, HasTimestamps, IsPublishable, IsReportable, IsTrashable, HasSlug;
	
	/**
	 * @var string
	 */
	const SLUGGABLE_FIELD = 'body';
	
	/**
	 * @Groups({"default"})
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank()
	 * @Assert\Length(min="10", max="280", minMessage="Please enter a minimum of 10 characters!", maxMessage="No more than 280 characters allowed!")
	 */
	protected $body;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="microPosts")
	 * @ORM\JoinColumn(nullable=false)
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var Authorable
	 */
	protected $author;
	
	/**
	 * @Groups({"default", "user-with-posts"})
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="microPostsLiked", cascade={"all"})
	 * @ORM\JoinTable(name="micro_post_likes",
	 *     joinColumns={
	 *          @ORM\JoinColumn(name="micro_post_id", referencedColumnName="id", nullable=false)
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
	 * @ORM\OneToMany(targetEntity="App\Entity\MicroComment", mappedBy="microPost", cascade={"all"})
	 * @var Collection
	 */
	protected $comments;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="reportedMicroPosts")
	 * @ORM\JoinTable(name="reported_micro_posts", joinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 *      },
	 *      inverseJoinColumns={
	 *          @ORM\JoinColumn(name="micro_post_id", referencedColumnName="id", nullable=false)
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
	
	/**
	 * @return string
	 */
	public function getBody(): ?string
	{
		return $this->body;
	}
	
	/**
	 * @param  string  $body
	 * @return MicroPost
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
}
