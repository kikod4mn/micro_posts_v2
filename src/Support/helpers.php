<?php

/**
 * Modified helpers file from Laravel.
 * Laravel - A PHP Framework For Web Artisans
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

use App\Support\Arr;
use Doctrine\Common\Collections\Collection;

if (! function_exists('data_get')) {
	/**
	 * Get an item from an array or object using "dot" notation.
	 * @param  mixed                  $target
	 * @param  string|array|int|null  $key
	 * @param  mixed                  $default
	 * @return mixed
	 */
	function data_get($target, $key, $default = null)
	{
		if (is_null($key)) {
			return $target;
		}
		
		$key = is_array($key) ? $key : explode('.', $key);
		
		foreach ($key as $i => $segment) {
			unset($key[$i]);
			
			if (is_null($segment)) {
				return $target;
			}
			
			if ($segment === '*') {
				if ($target instanceof Collection) {
					$target = $target->all();
				} else if (! is_array($target)) {
					return value($default);
				}
				
				$result = [];
				
				foreach ($target as $item) {
					$result[] = data_get($item, $key);
				}
				
				return in_array('*', $key) ? Arr::collapse($result) : $result;
			}
			
			if (Arr::accessible($target) && Arr::exists($target, $segment)) {
				$target = $target[$segment];
			} else if (is_object($target) && isset($target->{$segment})) {
				$target = $target->{$segment};
			} else {
				return value($default);
			}
		}
		
		return $target;
	}
}

if (! function_exists('blank')) {
	/**
	 * Determine if the given value is "blank".
	 * @param  mixed  $value
	 * @return bool
	 */
	function blank($value)
	{
		if (is_null($value)) {
			return true;
		}
		
		if (is_string($value)) {
			return trim($value) === '';
		}
		
		if (is_numeric($value) || is_bool($value)) {
			return false;
		}
		
		if ($value instanceof Countable) {
			return count($value) === 0;
		}
		
		return empty($value);
	}
}

if (! function_exists('filled')) {
	/**
	 * Determine if a value is "filled".
	 * @param  mixed  $value
	 * @return bool
	 */
	function filled($value)
	{
		return ! blank($value);
	}
}

if (! function_exists('head')) {
	/**
	 * Get the first element of an array. Useful for method chaining.
	 * @param  array  $array
	 * @return mixed
	 */
	function head($array)
	{
		return reset($array);
	}
}

if (! function_exists('last')) {
	/**
	 * Get the last element from an array.
	 * @param  array  $array
	 * @return mixed
	 */
	function last($array)
	{
		return end($array);
	}
}

if (! function_exists('value')) {
	/**
	 * Return the default value of the given value.
	 * @param  mixed  $value
	 * @return mixed
	 */
	function value($value)
	{
		return $value instanceof Closure ? $value() : $value;
	}
}
