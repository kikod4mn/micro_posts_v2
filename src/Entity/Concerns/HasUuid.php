<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

trait HasUuid
{
	/**
	 * @Groups(
	 *     {"default", "administer", "user-with-posts", "user-with-comments", "user-with-followers", "user-with-following"}
	 *     )
	 * @ORM\Id()
	 * @ORM\Column(type="bigint", options={"unsigned": true})
	 * @ORM\GeneratedValue()
	 * @var int|null
	 */
	protected $id;
	
	/**
	 * @Groups(
	 *     {"default", "administer", "user-with-posts", "user-with-comments", "user-with-followers", "user-with-following"}
	 *     )
	 * @ORM\Column(type="uuid", unique=true)
	 * @var UuidInterface
	 */
	protected $uuid;
	
	/**
	 * @return null|int
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
	/**
	 * @return null|string
	 */
	public function getUuid(): ?string
	{
		return $this->uuid;
	}
	
	public function generateUuid(): void
	{
		$this->uuid = Uuid::uuid4();
	}
}