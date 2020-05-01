<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Contracts\Authorable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="`micro_post_comment`")
 * @UniqueEntity(fields="uuid", message="How did this happen???? Uuid should be unique!!")
 * @UniqueEntity(fields="slug", message="How did this happen???? Slug should be unique!!")
 * @ORM\Entity(repositoryClass="App\Repository\MicroPostCommentRepository")
 */
class MicroComment extends BaseComment
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\MicroPost", inversedBy="comments")
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var Collection
	 */
	protected $microPost;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="microComments")
	 * @ORM\JoinColumn(nullable=false)
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var Authorable
	 */
	protected $author;
	
	/**
	 * @ORM\ManyToMany(targetEntity="User", inversedBy="microCommentsLiked")
	 * @ORM\JoinTable(name="micro_comment_likes",
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
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="reportedMicroComments")
	 * @ORM\JoinTable(name="reported_micro_comments", joinColumns={
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
	 * Micro Post Comment constructor.
	 */
	public function __construct()
	{
		$this->likedBy    = new ArrayCollection();
		$this->microPost  = new ArrayCollection();
		$this->reportedBy = new ArrayCollection();
	}
	
	/**
	 * @return null|Collection
	 */
	public function getMicroPost()
	{
		return $this->microPost;
	}
	
	/**
	 * @param  MicroPost  $microPost
	 */
	public function setMicroPost(MicroPost $microPost): void
	{
		$this->microPost = $microPost;
	}
}