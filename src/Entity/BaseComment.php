<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\IsReportable;
use App\Entity\Concerns\CountsLikes;
use App\Entity\Concerns\CountsViews;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\IsLikable;
use App\Entity\Concerns\HasSlug;
use App\Entity\Concerns\HasTimestamps;
use App\Entity\Concerns\IsPublishable;
use App\Entity\Concerns\HasUuid;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\CountableLikes;
use App\Entity\Contracts\CountableViews;
use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\Reportable;
use App\Entity\Contracts\Sluggable;
use App\Entity\Contracts\TimeStampable;
use App\Entity\Contracts\Uniqable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "blog_comment" = "App\Entity\BlogComment",
 *     "micro_comment" = "App\Entity\MicroComment"
 * })
 * @ORM\MappedSuperclass()
 */
abstract class BaseComment extends AbstractEntity
	implements CountableViews, CountableLikes, Authorable, TimeStampable, Publishable, Reportable, Uniqable, Sluggable
{
	use HasUuid, CountsLikes, CountsViews, HasAuthor, HasTimestamps, IsPublishable, IsReportable, HasSlug, IsLikable;
	
	/**
	 * @var string
	 */
	const SLUGGABLE_FIELD = 'body';
	
	/**
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank()
	 * @Assert\Length(min="10", max="280", minMessage="Please enter a minimum of 10 characters!", maxMessage="No more than 280 characters allowed!")
	 */
	protected $body;
	
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
	 * @return string
	 */
	public function getBody(): ?string
	{
		return $this->body;
	}
	
	/**
	 * @param  string  $body
	 * @return BaseComment
	 */
	public function setBody(string $body): self
	{
		$this->body = $this->cleanString($body);
		
		return $this;
	}
}
