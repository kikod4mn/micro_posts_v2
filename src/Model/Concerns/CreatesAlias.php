<?php

declare(strict_types = 1);

namespace App\Model\Concerns;

use App\Entity\Contracts\Publishable;
use App\Entity\Contracts\Trashable;
use App\Support\Str;

trait CreatesAlias
{
	/**
	 * Generate an alias in the constructor for the repository.
	 * @var string
	 */
	protected $alias;
	
	/**
	 * The fqn of the entity for the model.
	 * @var string
	 */
	protected $entity;
	
	/**
	 * @var bool
	 */
	protected $_e_trashable;
	
	/**
	 * @var bool
	 */
	protected $_e_publishable;
	
	/**
	 * Create an alias for the Doctrine QB from the class name.
	 * Set the entity namespace.
	 * @return void
	 */
	protected function createAliases(): void
	{
		// Set the entity fqn.
		$this->entity = $this->repository->getClassName();
		
		// Set defaults to look for in filtering and abstract model.
		$this->_e_trashable   = is_a($this->entity, Trashable::class, true);
		$this->_e_publishable = is_a($this->entity, Publishable::class, true);
		
		// Generate an alias from the capital letters of the entity class.
		$this->alias = strtolower(
			preg_replace(
				'~[^A-Z]~',
				'',
				Str::afterLast($this->entity, '\\')
			)
		);
	}
}