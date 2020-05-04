<?php

declare(strict_types = 1);

namespace App\Support\Contracts;

interface FilterInterface
{
	/**
	 * @param  array  $data
	 * @param  array  $options
	 * @return array|object
	 */
	public function filter(array $data, array $options = []);
	
	/**
	 * @param  string    $name
	 * @param  callable  $filter
	 * @return $this|FilterInterface
	 */
	public function addFilter(string $name, callable $filter): FilterInterface;
	
	/**
	 * @param  string  $name
	 * @return $this|FilterInterface
	 */
	public function removeFilter(string $name): FilterInterface;
}