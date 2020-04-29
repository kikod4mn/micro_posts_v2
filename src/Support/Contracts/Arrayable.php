<?php

declare(strict_types = 1);

namespace App\Support\Contracts;

interface Arrayable
{
	/**
	 * Turn this object to array.
	 * @return array
	 */
	public function toArray(): array;
}