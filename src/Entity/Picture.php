<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\CountsLikes;
use App\Entity\Concerns\CountsViews;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\HasSlug;
use App\Entity\Concerns\HasTimestamps;
use App\Entity\Concerns\HasUuid;
use App\Entity\Concerns\IsLikable;
use App\Entity\Concerns\IsPublishable;
use App\Entity\Concerns\IsReportable;
use App\Entity\Concerns\IsTrashable;
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

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 */
class Picture extends AbstractEntity
	implements Uniqable, TimeStampable, Authorable, CountableViews, CountableLikes, Publishable, Reportable, Trashable, Sluggable
{
	use HasUuid, HasTimestamps, HasAuthor, CountsViews, CountsLikes, IsPublishable, IsReportable, IsTrashable, HasSlug, IsLikable;
	
	/**
	 * @var string
	 */
	const SLUGGABLE_FIELD = 'title';
	
	/**
	 * NOTE : In notification context, author means the user it is generated for.
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="pictures", fetch="EAGER")
	 * @ORM\JoinColumn(nullable=false)
	 * @var Authorable|User
	 */
	protected $author;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	protected $title;
	
	/**
	 * @ORM\Column(type="text", length=2000, nullable=true)
	 * @var string
	 */
	protected $description;
	
	/**
	 * @ORM\Column(type="text", length=500, nullable=false)
	 * @var string
	 */
	protected $thumbnail;
	
	/**
	 * @ORM\Column(type="text", length=500, nullable=false)
	 * @var string
	 */
	protected $placeholder;
	
	/**
	 * @ORM\Column(type="text", length=500, nullable=false)
	 * @var string
	 */
	protected $original;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\BlogPost", inversedBy="headerImage")
	 * @ORM\JoinTable(name="blog_post_header_image",
	 *     joinColumns={
	 *         @ORM\JoinColumn(name="picture_id", referencedColumnName="id", nullable=false)
	 *     },
	 *     inverseJoinColumns={
	 *         @ORM\JoinColumn(name="blog_post_id", referencedColumnName="id", nullable=false)
	 *     }
	 * )
	 * @var BlogPost[]|Collection
	 */
	protected $blogPost;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Gallery", inversedBy="pictures")
	 * @ORM\JoinTable(name="gallery_pictures",
	 *     joinColumns={
	 *         @ORM\JoinColumn(name="picture_id", referencedColumnName="id", nullable=false)
	 *     },
	 *     inverseJoinColumns={
	 *         @ORM\JoinColumn(name="gallery_id", referencedColumnName="id", nullable=false)
	 *     }
	 * )
	 * @var Gallery[]|Collection
	 */
	protected $gallery;
	
	/**
	 * Picture constructor.
	 */
	public function __construct()
	{
		$this->blogPost = new ArrayCollection();
		$this->gallery  = new ArrayCollection();
	}
	
	/**
	 * @return null|string
	 */
	public function getTitle(): ?string
	{
		return $this->title;
	}
	
	/**
	 * @param  string  $title
	 * @return $this|Picture
	 */
	public function setTitle(string $title): Picture
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
	 * @return $this|Picture
	 */
	public function setDescription(string $description): Picture
	{
		$this->description = $this->cleanString($description);
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getThumbnail(): ?string
	{
		return $this->thumbnail;
	}
	
	/**
	 * @param  string  $thumbnail
	 * @return $this|Picture
	 */
	public function setThumbnail(string $thumbnail): Picture
	{
		$this->thumbnail = $thumbnail;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getPlaceholder(): ?string
	{
		return $this->placeholder;
	}
	
	/**
	 * @param  string  $placeholder
	 * @return $this|Picture
	 */
	public function setPlaceholder(string $placeholder): Picture
	{
		$this->placeholder = $placeholder;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getOriginal(): ?string
	{
		return $this->original;
	}
	
	/**
	 * @param  string  $original
	 * @return $this|Picture
	 */
	public function setOriginal(string $original): Picture
	{
		$this->original = $original;
		
		return $this;
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
	 * @return $this|Picture
	 */
	public function setBlogPost(BlogPost $blogPost): Picture
	{
		$this->blogPost = $blogPost;
		
		return $this;
	}
	
	/**
	 * @return null|Collection|Gallery
	 */
	public function getGallery(): ?Collection
	{
		return $this->gallery;
	}
	
	/**
	 * @param  Gallery  $gallery
	 * @return $this|Picture
	 */
	public function setGallery(Gallery $gallery): Picture
	{
		$this->gallery = $gallery;
		
		return $this;
	}
}
