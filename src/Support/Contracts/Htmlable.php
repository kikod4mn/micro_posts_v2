<?php

declare(strict_types = 1);

namespace App\Support\Contracts;

interface Htmlable
{
	/**
	 * Implement a field to return as html from the class.
	 * @return string
	 */
	public function toHtml(): string;
}