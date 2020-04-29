<?php

declare(strict_types = 1);

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