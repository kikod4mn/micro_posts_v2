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
	 * NOTE : Symfony does not like strict types, attempt to cast the Id to an integer before returning.
	 */
	public function getId(): ?int
	{
		return (int) $this->id;
	}
	
	/**
	 * @return null|string
	 * NOTE : Symfony does not like strict types, attempt to cast the Uuid to a string before returning.
	 */
	public function getUuid(): ?string
	{
		return (string) $this->uuid;
	}
	
	/**
	 * Generate a uuid for the entity.
	 */
	public function generateUuid(): void
	{
		$this->uuid = Uuid::uuid4();
	}
}