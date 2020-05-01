<?php

declare(strict_types = 1);

namespace App\Security;

use App\Entity\Contracts\Trashable;
use App\Entity\User;
use App\Security\Exception\AccountDeletedException;
use App\Security\Exception\AccountNotActiveException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
	/**
	 * @param  UserInterface  $user
	 */
	public function checkPreAuth(UserInterface $user): void
	{
		// Only allow instances of our User class to be checked
		if (! $user instanceof User) {
			
			return;
		}
		
		// If user has been trashed, throw Exception and prevent login.
		if ($user instanceof Trashable && $user->isTrashed()) {
			
			throw new AccountDeletedException();
		}
	}
	
	/**
	 * @param  UserInterface  $user
	 */
	public function checkPostAuth(UserInterface $user): void
	{
		// Only allow instances of our User class to be checked
		if (! $user instanceof User) {
			
			return;
		}
		
		// If the user is not yet activated, throw Exception and prevent further action.
		if (! $user->isActivated()) {
			
			throw new AccountNotActiveException();
		}
	}
	
}