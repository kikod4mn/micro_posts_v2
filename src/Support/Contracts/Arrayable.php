<?php

namespace App\Support\Contracts;

interface Arrayable
{
	/**
	 * Turn this object to array.
	 * @return array
	 */
	public function toArray(): array;
}