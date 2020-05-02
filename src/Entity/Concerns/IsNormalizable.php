<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\Normalizable;
use Carbon\Carbon;
use DateTimeInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

trait IsNormalizable
{
	/**
	 * NOTE : Group default is always included for fields in traits that are commonly needed across entities and different ways of display.
	 * @param  string|string[]  $groups
	 * @return array
	 */
	public function toArray($groups = []): array
	{
		$groups[] = 'default';
		
		if (! $this->serializer) {
			
			$this->createSerializer();
		}
		
		//		if ($this instanceof TimeStampable && true === $this->hasTimestamps()) {
		//
		//			$this->createdAt = $this->serializeDate($this->getCreatedAt());
		//
		//			if (! is_null($this->updatedAt)) {
		//
		//				$this->updatedAt = $this->serializeDate($this->getUpdatedAt());
		//			}
		//
		//			if ($this instanceof Trashable && ! is_null($this->getTrashedAt())) {
		//
		//				$this->trashedAt = $this->serializeDate($this->getTrashedAt());
		//			}
		//
		//			if ($this instanceof Publishable && ! is_null($this->getPublishedAt())) {
		//
		//				$this->publishedAt = $this->serializeDate($this->getPublishedAt());
		//			}
		//		}
		
		return $this->serializer->normalize($this, null, ['groups' => $groups]);
	}
	
	/**
	 * @param  SerializerInterface  $serializer
	 * @return $this|Normalizable
	 */
	public function setSerializer(SerializerInterface $serializer): Normalizable
	{
		$this->serializer = $serializer;
		
		return $this;
	}
	
	/**
	 * @return SerializerInterface
	 */
	public function getSerializer(): SerializerInterface
	{
		return $this->serializer;
	}
	
	/**
	 * @return Normalizable
	 */
	protected function createSerializer(): Normalizable
	{
		$classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
		
		$dateTimeCallback = function ($dateTime) {
			return $dateTime instanceof DateTimeInterface
				? Carbon::instance($dateTime)->toDateTimeString()
				: null;
		};
		
		$defaultContext = [
			ObjectNormalizer::CALLBACKS => [
				'createdAt'   => $dateTimeCallback,
				'updatedAt'   => $dateTimeCallback,
				'trashedAt'   => $dateTimeCallback,
				'publishedAt' => $dateTimeCallback,
			],
		];
		
		$objectNormalizer = new ObjectNormalizer(
			$classMetadataFactory,
			null,
			null,
			null,
			null,
			null,
			$defaultContext
		);
		
		$serializer = new Serializer([$objectNormalizer]);
		
		return $this->setSerializer($serializer);
	}
}