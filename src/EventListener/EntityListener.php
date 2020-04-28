<?php

namespace App\EventListener;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\TimeStampable;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

class EntityListener
{
	/**
	 * @var Security
	 */
	private $security;
	
	/**
	 * EntityListener constructor.
	 * @param  Security  $security
	 */
	public function __construct(Security $security)
	{
		$this->security = $security;
	}
	
	/**
	 * Set the timestamps and author.
	 * TimeStamps is assumed to exist on an authored entity.
	 * @param  LifecycleEventArgs  $args
	 */
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		
		if (!$entity instanceof TimeStampable) {
			
			return;
		}
		
		if ($entity instanceof TimeStampable) {
			
			$entity->setCreationTimeStamps();
		}
		
		if (!$entity instanceof Authorable) {
			
			return;
		}
		
		// For author
		if ($entity instanceof Authorable) {
			
			if (!$entity->getAuthor()) {
				
				$entity->setAuthor($this->security->getUser());
			}
		}
	}
	
	public function preUpdate(LifecycleEventArgs $args)
	{
		// If $entity is instance of neither, we return.
		if (!$args->getEntity() instanceof TimeStampable) {
			
			return;
		}
		
		$args->getEntity()->setUpdatedTimestamps();
	}
	
	public function postLoad(LifecycleEventArgs $args)
	{
		/** @var AbstractEntity $entity */
		$entity = $args->getEntity();
		
		// If our entity is not extending abstract entity, no need for the entity manager to be set.
		if (!$entity instanceof AbstractEntity) {
			
			return;
		}
		
		$entity->setEntityManager($args->getEntityManager());
	}
}