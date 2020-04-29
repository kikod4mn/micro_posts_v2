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
	public function checkPreAuth(UserInterface $user)
	{
		if (!$user instanceof User) {
			
			return;
		}
		if ($user instanceof Trashable && $user->isTrashed()) {
			
			throw new AccountDeletedException();
		}
	}
	
	public function checkPostAuth(UserInterface $user)
	{
		if (!$user instanceof User) {
			
			return;
		}
		if (!$user->isActivated()) {
			
			throw new AccountNotActiveException();
		}
	}
	
}