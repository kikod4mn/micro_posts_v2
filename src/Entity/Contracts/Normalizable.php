<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

use Symfony\Component\Serializer\SerializerInterface;

interface Normalizable
{
	/**
	 * @param  string|string[]  $groups
	 * @return array
	 */
	public function toArray($groups = 'default'): array ;
	
	/**
	 * @param  SerializerInterface  $serializer
	 * @return $this|Normalizable
	 */
	public function setSerializer(SerializerInterface $serializer): Normalizable;
	
	/**
	 * @return SerializerInterface
	 */
	public function getSerializer(): SerializerInterface;
}