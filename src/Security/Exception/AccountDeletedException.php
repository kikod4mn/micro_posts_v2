<?php

declare(strict_types = 1);

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AccountDeletedException extends AccountStatusException
{
	/**
	 * {@inheritdoc}
	 */
	public function getMessageKey()
	{
		return 'Account has been deleted. Please check your mailbox for a contact email regarding this issue and if the email is not there, contact an admin.';
	}
}