<?php

namespace App\Support\Contracts;

interface Jsonable
{
	/**
	 * Convert this object to a json representation.
	 * @param  int  $options
	 * @return string
	 */
	public function toJson(int $options = 0): string;
}