<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\HasSlug;
use App\Entity\Concerns\HasUuid;
use App\Entity\Concerns\IsPublishable;
use App\Entity\Concerns\IsTrashable;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\Sluggable;
use App\Entity\Contracts\Trashable;
use App\Entity\Contracts\Uniqable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GalleryRepository")
 */
class Gallery extends AbstractEntity implements Uniqable, Authorable, Publishable, Trashable, Sluggable
{
	use HasUuid, HasAuthor, IsPublishable, IsTrashable, HasSlug;
	
	/**
	 * @var string
	 */
	const SLUGGABLE_FIELD = 'title';
	
	/**
	 * NOTE : In notification context, author means the user it is generated for.
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="galleries")
	 * @ORM\JoinColumn(nullable=false)
	 * @var Authorable|User|Collection
	 */
	protected $author;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 * @var string
	 */
	protected $title;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Picture", mappedBy="gallery")
	 * @var Picture[]|Collection
	 */
	protected $pictures;
	
	/**
	 * @ORM\Column(type="text", length=2000, nullable=true)
	 * @var string
	 */
	protected $description;
	
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\BlogPost", mappedBy="gallery")
	 * @var BlogPost|Collection
	 */
	protected $blogPost;
	
	public function __construct()
	{
		$this->author   = new ArrayCollection();
		$this->pictures = new ArrayCollection();
		$this->blogPost = new ArrayCollection();
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
	 * @return Gallery
	 */
	public function setTitle(string $title): self
	{
		$this->title = $this->cleanString($title);
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}
	
	/**
	 * @param  string  $description
	 * @return Gallery
	 */
	public function setDescription(string $description): self
	{
		$this->description = $this->cleanString($description);
		
		return $this;
	}
	
	/**
	 * @return null|Collection|Picture[]
	 */
	public function getPictures(): ?Collection
	{
		return $this->pictures;
	}
	
	/**
	 * @param  Picture  $picture
	 * @return Gallery
	 */
	public function addPicture(Picture $picture): self
	{
		if (! $this->hasPicture($picture)) {
			
			$this->pictures->add($picture);
		}
		
		return $this;
	}
	
	/**
	 * Verify if gallery already has a picture in it.
	 * @param  Picture  $picture
	 * @return bool
	 */
	public function hasPicture(Picture $picture): bool
	{
		return $this->pictures->contains($picture);
	}
	
	/**
	 * @return null|Collection|BlogPost
	 */
	public function getBlogPost(): ?Collection
	{
		return $this->blogPost;
	}
	
	/**
	 * Publish a Gallery only if it does not belong to a blog post.
	 * @return $this|Publishable
	 */
	public function publish(): Publishable
	{
		if (! $this->hasBlogPost()) {
			
			$this->published = true;
		}
		
		return $this;
	}
	
	/**
	 * Checks to see if the current gallery has a blog post.
	 * @return bool
	 */
	public function hasBlogPost(): bool
	{
		return ! is_null($this->blogPost) || ! $this->blogPost->isEmpty();
	}
}
