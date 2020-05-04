<?php

declare(strict_types = 1);

namespace App\Support;

use App\Support\Contracts\FilterInterface;

class Filter implements FilterInterface
{
	/**
	 * @var array
	 */
	private $filters = [];
	
	/**
	 * Define a callback function to evaluate the entity.
	 * Return null in the case of filter pass failure and entity itself when successful.
	 * Failures are removed from the array and a clean array is returned.
	 * @param  string    $name
	 * @param  callable  $filter
	 * @return $this|FilterInterface
	 */
	public function addFilter(string $name, callable $filter): FilterInterface
	{
		$this->filters[$name] = $filter;
		
		return $this;
	}
	
	/**
	 * @param  string  $name
	 * @return $this|FilterInterface
	 */
	public function removeFilter(string $name): FilterInterface
	{
		$this->filters = $this->removeOffset($name, $this->filters);
		
		return $this;
	}
	
	/**
	 * @param  array|object  $data
	 * @param  array         $options
	 * @return array|object
	 */
	public function filter($data, array $options = [])
	{
		if (is_array($data)) {
			
			return $this->multiple($data, $options);
			
		} else {
			
			return $this->singular($data, $options);
		}
	}
	
	/**
	 * @param  object  $datum
	 * @param  array   $options
	 * @return null|object
	 */
	private function singular(object $datum, array $options): ?object
	{
		foreach ($this->filters as $filter) {
			$datum = $filter($datum);
		}
		
		return ! empty($datum) ? $datum : null;
	}
	
	/**
	 * @param  array  $data
	 * @param  array  $options
	 * @return array
	 */
	private function multiple(array $data, array $options): array
	{
		$returnData = [];
		
		foreach ($data as $datum) {
			
			$datum = $this->singular($datum, $options);
			
			// If the filtering did not unset the entity add it to the return.
			if (! empty($datum)) {
				
				$returnData[] = $datum;
			}
		}
		
		return $returnData;
	}
	
	/**
	 * @param         $offset
	 * @param  array  $data
	 * @return array
	 */
	private function removeOffset($offset, array $data): array
	{
		return array_splice($data, $offset, 1);
	}
}