<?php

namespace App\Support\Concerns;

use App\Entity\Abstracts\AbstractEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;

trait HasAttributes
{
	protected function attributesToArray(): array
	{
		if (!$this->toArray || [] === $this->toArray) {
			
			return ['id' => $this->getId()];
		}
		
		$return = [];
		
		foreach ($this->toArray as $property) {
			
			$return[$property] = $this->{$property};
		}
		
		return $return;
	}
	
	protected function relationsToArray(): array
	{
		$return = [];
		
		dump($this->em->getClassMetaData(get_class($this)));
		
		/** @var ClassMetadata $metaData */
		$metaData = $this->em->getClassMetaData(get_class($this));
		
		$mappings = $metaData->getAssociationMappings();
		
		foreach ($mappings as $mapping) {
			$return[$mapping['fieldName']] = ($this->getFormattedRelations($mapping));
		}
		
		return $return;
	}
	
	protected function getFormattedRelations($mapping)
	{
		$return    = [];
		$relations = $this->getRawRelation($mapping);
		
		if ($relations instanceof Collection) {
			
			return $this->returnCollectionRelationToArray($relations);
		} else {
			
			return $this->getDataAsArray($relations);
		}
	}
	
	protected function getRawRelation($mapping)
	{
		return $this->{'get' . ucfirst($mapping['fieldName'])}();
	}
	
	protected function returnCollectionRelationToArray(Collection $relations)
	{
		foreach ($relations as $relation) {
			$return[$relation] = $this->getDataAsArray($relation);
		}
	}
	
	protected function getDataAsArray($relation)
	{
		if (!$relation instanceof AbstractEntity) {
			return [$relation->getId()];
		}
		
		return $relation->attributesToArray();
	}
}