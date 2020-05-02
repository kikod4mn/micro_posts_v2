<?php

declare(strict_types = 1);

namespace App\Entity\Abstracts;

use App\Entity\Concerns\IsNormalizable;
use App\Entity\Contracts\Normalizable;
use App\Entity\Contracts\Trashable;
use App\Support\Contracts\Arrayable;
use App\Support\Contracts\Jsonable;
use App\Support\Contracts\Stringable;
use Crudle\Profanity\Dictionary\GB;
use Crudle\Profanity\Dictionary\US;
use Crudle\Profanity\Filter;
use Doctrine\ORM\EntityManagerInterface;
use HTMLPurifier;
use HTMLPurifier_Config;
use InvalidArgumentException;
use JsonSerializable;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractEntity implements Arrayable, Jsonable, JsonSerializable, Stringable, Normalizable
{
	use IsNormalizable;
	
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
	 * NOTE : Group default is always included for fields in traits that are commonly needed across entities and different ways of display.
	 * @param  array|string[]  $groups  Normalization context groups
	 * @param  int              $options
	 * @return string
	 */
	public function toJson(array $groups = [], int $options = 0): string
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
	
	/**
	 * Clean the string from html and profanities. No validation, just cleaning.
	 * @param  string  $text
	 * @return string
	 */
	protected function cleanString(string $text): string
	{
		return $this->filterHTML(
			$this->filterProfanities(
				$text
			)
		);
	}
	
	/**
	 * Filter out HTML tags, allowed tags are kept.
	 * @param  string  $text
	 * @return string
	 */
	protected function filterHTML(string $text): string
	{
		return $this->htmlPurifier()->purify($text);
	}
	
	/**
	 * @return HTMLPurifier
	 */
	protected function htmlPurifier(): HTMLPurifier
	{
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.Allowed', $this->allowedHtmlTags());
		
		return new HTMLPurifier($config);
	}
	
	/**
	 * Get the custom allowed tags with defaults or return just defaults.
	 * @return string
	 */
	protected function allowedHtmlTags(): string
	{
		return defined('static::HTML_TAGS_ALLOWED')
			? implode(',', static::HTML_TAGS_ALLOWED) . ',p,strong,i,a[href]'
			: 'p,strong,i,a[href]';
	}
	
	/**
	 * Cleans the string of profanities. Does not validate, just cleans.
	 * @param  string  $text
	 * @return string
	 */
	protected function filterProfanities(string $text): string
	{
		foreach ($this->profanityDictionaries() as $dict) {
			
			$text = (new Filter($dict))->cleanse($text);
		}
		
		return $text;
	}
	
	/**
	 * Determine if the string contains profanities. Only checks, does not cleanse.
	 * @param  string  $text
	 * @return bool
	 */
	protected function containsProfanities(string $text): bool
	{
		foreach ($this->profanityDictionaries() as $dict) {
			
			if ((new Filter($dict))->isDirty($text)) {
				
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Return custom filters together with defaults or just the defaults.
	 * @return array
	 */
	protected function profanityDictionaries(): array
	{
		$defaults = [new GB(), new US()];
		
		return defined('static::PROFANITY_FILTERS') ? array_merge(static::PROFANITY_FILTERS, $defaults) : $defaults;
	}
}