<?php

namespace App\Entity\Concerns;

use App\Entity\Contracts\Sluggable;
use App\Support\Str;
use Doctrine\ORM\Mapping as ORM;

trait HasSlug
{
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var null|string
	 */
	protected $slug;
	
	/**
	 * First makes sure the slug passed in is not too long.
	 * If null is passed, assumes the presence of "title" field.
	 * If title does not exist, assumes presence of "static::SLUGGABLE_FIELD".
	 * If neither is present on the entity and no slug is passed in, sets the slug to a string of 'null-slug' and appends a pseudo random bit of gibberish.
	 * @param  null|string  $sluggable
	 * @return $this|Sluggable
	 */
	public function setSlug(?string $sluggable): Sluggable
	{
		if (Str::length($sluggable) > 100) {
			
			$sluggable = Str::limit($sluggable, 100, ['']);
		}
		
		if (! is_null($sluggable)) {
			
			$this->slug = Str::slug($sluggable);
			
			return $this;
		}
		
		if (property_exists($this, 'title')) {
			
			$this->slug = Str::slug($this->title);
			
			return $this;
		}
		
		if (defined('static::SLUGGABLE_FIELD')) {
			
			$this->slug = Str::slug($this->{static::SLUGGABLE_FIELD});
			
			return $this;
		}
		
		$this->slug = 'null-slug-' . Str::random(34);
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getSlug(): ?string
	{
		return $this->slug;
	}
}