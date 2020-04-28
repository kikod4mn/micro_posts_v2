<?php

namespace App\Entity\Abstracts;

use App\Entity\Concerns\CanNormalize;
use App\Entity\Contracts\Normalizable;
use App\Entity\Contracts\Trashable;
use App\Support\Concerns\HasAttributes;
use App\Support\Contracts\Arrayable;
use App\Support\Contracts\Jsonable;
use App\Support\Contracts\Stringable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use JsonSerializable;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractEntity implements Arrayable, Jsonable, JsonSerializable, Stringable, Normalizable
{
	use HasAttributes, CanNormalize;
	
	/**
	 * Default name for created at timestamp field.
	 * @var string
	 */
	const CREATED_AT = 'createdAt';
	
	/**
	 * Default name for updated at timestamp field.
	 * @var string
	 */
	const UPDATED_AT = 'updatedAt';
	
	/**
	 * @var EntityManagerInterface
	 */
	protected $em = null;
	
	/**
	 * @var SerializerInterface
	 */
	protected $serializer;
	
	/**
	 * @param  EntityManagerInterface  $em
	 * @return $this
	 */
	public function setEntityManager(EntityManagerInterface $em): self
	{
		$this->em = $em;
		
		return $this;
	}
	
	/**
	 * @return null|EntityManagerInterface
	 */
	public function getEntityManager(): ?EntityManagerInterface
	{
		return $this->em;
	}
	
	/**
	 * @return array|mixed
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}
	
	/**
	 * @param  int              $options
	 * @param  string|string[]  $groups  Normalization context groups
	 * @return string
	 */
	public function toJson(int $options = 0, $groups = 'default'): string
	{
		return json_encode($this->toArray($groups), $options);
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson();
	}
	
	/**
	 * @param  int|string  $name
	 * @return mixed
	 */
	public function getAttribute($name)
	{
		if (! property_exists($this, $name)) {
			
			throw new InvalidArgumentException(
				sprintf('WARNING : Property "%s" not found on "%s".', (string) $name, get_class($this))
			);
		}
		
		return $this->{$name};
	}
	
	/**
	 * Fluent interface abstract setter.
	 * @param         $name
	 * @param  mixed  $value
	 * @return $this
	 */
	public function setAttribute($name, $value): self
	{
		if (! property_exists($this, $name)) {
			
			throw new InvalidArgumentException(
				sprintf('WARNING : Property "%s" not found on "%s".', (string) $name, get_class($this))
			);
		}
		
		$this->{$name} = $value;
		
		return $this;
	}
	
	/**
	 * Delete an entity from the database.
	 */
	public function delete(): bool
	{
		// If entity does not implement trashable, delete it right away and return success.
		if (! $this instanceof Trashable) {
			$this->forceDelete();
			
			return true;
		}
		
		// If the entity implements Trashable, check if it is trashed.
		// If not trashed, we should notify the coder of the need to first implement the delete or call the forceDelete method instead.
		if (! $this->isTrashed()) {
			
			return false;
		}
		
		$this->forceDelete();
		
		return true;
	}
	
	/**
	 * Delete this entity from the database without checks or precautions.
	 * NOTE : if Foreign Key Constraints are set and not properly cascaded, will still fail.
	 */
	public function forceDelete(): void
	{
		$this->em->remove($this);
		$this->em->flush();
	}
}