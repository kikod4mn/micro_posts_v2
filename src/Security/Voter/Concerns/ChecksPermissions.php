<?php

declare(strict_types = 1);

namespace App\Security\Voter\Concerns;

use App\Entity\Contracts\Authorable;
use App\Entity\User;
use App\Entity\UserPreferences;
use App\Entity\UserProfile;
use Symfony\Component\Security\Core\User\UserInterface;

trait ChecksPermissions
{
	protected function isOwner($subject): bool
	{
		if (! $this->isUser()) {
			
			return false;
		}
		
		return $this->ownerCheckMethod($subject);
	}
	
	protected function ownerCheckMethod($subject): bool
	{
		switch ($subject) {
			case $subject instanceof Authorable:
				return $this->user->getId() === $subject->getAuthor()->getId();
			case $subject instanceof User:
				return $this->user->getId() === $subject->getId();
			case $subject instanceof UserProfile:
			case $subject instanceof UserPreferences:
			default:
				return $this->user->getId() === $subject->getUser()->getId();
		}
	}
	
	protected function isAdmin(): bool
	{
		return $this->security->isGranted(User::ROLE_ADMINISTRATOR);
	}
	
	protected function isUser(): bool
	{
		return ! is_null($this->user) && $this->user instanceof UserInterface;
	}
}