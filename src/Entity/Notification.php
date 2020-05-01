<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\HasTimestamps;
use App\Entity\Concerns\HasUuid;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\TimeStampable;
use App\Entity\Contracts\Uniqable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="`notification`")
 * @UniqueEntity(fields="uuid", message="How did this happen???? Uuid should be unique!!")
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "notification_base" = "App\Entity\Notification"
 * })
 * @ORM\MappedSuperclass()
 */
class Notification extends AbstractEntity implements Authorable, TimeStampable, Uniqable
{
	use HasUuid, HasAuthor, HasTimestamps;
	
	/**
	 * NOTE : In notification context, author means the user it is generated for.
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifications")
	 * @ORM\JoinColumn(nullable=false)
	 * @var Authorable|User|Collection
	 */
	protected $author;
	
	/**
	 * @ORM\Column(type="boolean", options={"default":false})
	 * @var bool
	 */
	protected $seen = false;
	
	/**
	 * @return bool
	 */
	public function isSeen(): ?bool
	{
		return $this->seen;
	}
	
	/**
	 * @param  bool  $seen
	 * @return Notification
	 */
	public function setSeen(bool $seen): self
	{
		$this->seen = $seen;
		
		return $this;
	}
}
