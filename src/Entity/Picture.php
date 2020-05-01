<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\CountsLikes;
use App\Entity\Concerns\CountsViews;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\HasTimestamps;
use App\Entity\Concerns\HasUuid;
use App\Entity\Concerns\IsPublishable;
use App\Entity\Concerns\IsReportable;
use App\Entity\Concerns\IsTrashable;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\CountableLikes;
use App\Entity\Contracts\CountableViews;
use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\Reportable;
use App\Entity\Contracts\TimeStampable;
use App\Entity\Contracts\Trashable;
use App\Entity\Contracts\Uniqable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 */
class Picture extends AbstractEntity implements Uniqable, TimeStampable, Authorable, CountableViews, CountableLikes, Publishable, Reportable, Trashable
{
	use HasUuid, HasTimestamps, HasAuthor, CountsViews, CountsLikes, IsPublishable, IsReportable, IsTrashable;
	
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
	 * @var Collection
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
	 * @var Collection
	 */
	protected $gallery;
}
