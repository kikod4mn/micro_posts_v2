<?php

declare(strict_types = 1);

namespace App\Security\Voter;

use App\Entity\MicroComment;
use App\Entity\User;
use App\Security\Voter\Concerns\ChecksPermissions;
use App\Security\Voter\Contracts\Actionable;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MicroCommentVoter extends Voter implements Actionable
{
	use ChecksPermissions;
	
	/**
	 * @var Security
	 */
	private $security;
	
	/**
	 * @var null|UserInterface|User
	 */
	private $user;
	
	/**
	 * AccountVoter constructor.
	 * @param  Security  $security
	 */
	public function __construct(Security $security)
	{
		$this->security = $security;
		$this->user     = $security->getUser();
	}
	
	/**
	 * @param  string  $attribute
	 * @param  mixed   $subject
	 * @return bool
	 */
	public function supports($attribute, $subject): bool
	{
		return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
			&& $subject instanceof MicroComment;
	}
	
	/**
	 * @param  string          $attribute
	 * @param  MicroComment    $subject
	 * @param  TokenInterface  $token
	 * @return bool
	 */
	protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
	{
		switch ($attribute) {
			case self::VIEW:
				return $this->isAdmin() || $this->isOwner($subject) || $subject->isPublished();
			case self::EDIT:
			case self::DELETE:
				return $this->isAdmin() || $this->isOwner($subject);
		}
		
		throw new LogicException('This code should not be reached!');
	}
}
