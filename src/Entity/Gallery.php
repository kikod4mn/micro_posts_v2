<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\HasUuid;
use App\Entity\Concerns\IsPublishable;
use App\Entity\Concerns\IsTrashable;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\Trashable;
use App\Entity\Contracts\Uniqable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GalleryRepository")
 */
class Gallery extends AbstractEntity implements Uniqable, Authorable, Publishable, Trashable
{
	use HasUuid, HasAuthor, IsPublishable, IsTrashable;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Picture", mappedBy="gallery")
	 * @var Collection
	 */
	protected $pictures;
	
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\BlogPost", inversedBy="gallery")
	 * @var Collection
	 */
	protected $blogPost;
}
