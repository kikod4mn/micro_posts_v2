<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\Normalizable;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

trait CanNormalize
{
	/**
	 * @param  string|string[]  $groups
	 * @return array
	 */
	public function toArray($groups = 'default'): array
	{
		if (!$this->serializer) {
			$this->createSerializer();
		}
		
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
		
		$objectNormalizer = new ObjectNormalizer($classMetadataFactory);
		
		$serializer = new Serializer([$objectNormalizer]);
		
		return $this->setSerializer($serializer);
	}
}