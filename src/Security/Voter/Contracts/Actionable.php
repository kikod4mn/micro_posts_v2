<?php

declare(strict_types = 1);

namespace App\Security\Voter\Contracts;

interface Actionable
{
	/**
	 * @var string
	 */
	const VIEW = 'VIEW';
	
	/**
	 * @var string
	 */
	const EDIT = 'EDIT';
	
	/**
	 * @var string
	 */
	const DELETE = 'DELETE';
}