<?php

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Concerns\HasAuthor;
use App\Entity\Concerns\HasTimestamps;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\TimeStampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "notification_base" = "App\Entity\Notification"
 * })
 * @ORM\MappedSuperclass()
 */
class Notification extends AbstractEntity implements Authorable, TimeStampable
{
	use HasAuthor, HasTimestamps;
	
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifications")
	 * @ORM\JoinColumn(nullable=false)
	 * @var Authorable
	 */
	protected $author;
	
	/**
	 * @ORM\Column(type="boolean", options={"default":false})
	 * @var bool
	 */
	protected $seen = false;
	
	/**
	 * @return null|int
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
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
