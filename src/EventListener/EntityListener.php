<?php

declare(strict_types = 1);

namespace App\EventListener;

use App\Entity\Abstracts\AbstractEntity;
use App\Entity\Contracts\Authorable;
use App\Entity\Contracts\CountableViews;
use App\Entity\Contracts\Sluggable;
use App\Entity\Contracts\TimeStampable;
use App\Entity\Contracts\Uniqable;
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
		
		// For generating a uuid for the entity.
		if ($entity instanceof Uniqable) {
			
			$entity->generateUuid();
		}
		
		// Set timestamps for created and updated fields.
		if ($entity instanceof TimeStampable) {
			
			$entity->setCreationTimeStamps();
		}
		
		// If the author is not set already manually, set the currently logged in user.
		if ($entity instanceof Authorable) {
			
			if (! $entity->getAuthor()) {
				// If no user is logged id, like with an api app, will throw an error.
				$entity->setAuthor($this->security->getUser());
			}
		}
		
		// If slug needs to be set and is not set yet, attempt to generate one.
		// See "setSlug" in the "HasSlug" trait.
		if ($entity instanceof Sluggable && null === $entity->getSlug()) {
			
			$entity->setSlug();
		}
	}
	
	/**
	 * @param  LifecycleEventArgs  $args
	 */
	public function preUpdate(LifecycleEventArgs $args)
	{
		// Only update timestamps if entity implements TimeStampable.
		if (! $args->getEntity() instanceof TimeStampable) {
			
			return;
		}
		
		$args->getEntity()->setUpdatedTimestamps();
	}
	
	/**
	 * @param  LifecycleEventArgs  $args
	 */
	public function postLoad(LifecycleEventArgs $args)
	{
		// If our entity is not extending abstract entity, no need for the entity manager to be set.
		if (! $args->getEntity() instanceof AbstractEntity) {
			
			return;
		}
		
		$entity = $args->getEntity();
		
		// Set the entity manager.
		$entity->setEntityManager($args->getEntityManager());
		
		// todo figure something better. This adds 200 ms on only 10 entites
		//		// If entity implements statistics gathering, for instance view counts, increment the counters.
		//		if ($entity instanceof CountableViews) {
		//
		//			$entity->incrementViewCounters();
		//		}
	}
}