<?php

declare(strict_types = 1);

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AccountNotActiveException extends AccountStatusException
{
	/**
	 * {@inheritdoc}
	 */
	public function getMessageKey()
	{
		return 'Account has not been activated yet. Please check your e-mail for the confirmation!';
	}
}