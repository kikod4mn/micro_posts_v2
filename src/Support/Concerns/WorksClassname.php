<?php

namespace App\Support\Concerns;

/**
 * Helper to aid in working with class names.
 */
trait WorksClassname
{
	/**
	 * Get the class name without namespace.
	 * @return string
	 */
	protected function withoutNamespace(): string
	{
		$classname = get_class($this);
		
		if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
		
		return $pos;
	}
	
	/**
	 * Create an alias for the Doctrine QB from the class name.
	 * @return string
	 */
	protected function getAliasForQB(): string
	{
		return strtolower(substr($this->withoutNamespace(), 0, 1));
	}
}