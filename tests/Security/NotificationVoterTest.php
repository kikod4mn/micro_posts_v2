<?php

declare(strict_types = 1);

namespace App\Tests\Security;

use App\Entity\Notification;
use App\Entity\User;
use App\Security\Voter\Contracts\Actionable;
use App\Security\Voter\NotificationVoter;
use App\Tests\Security\Concerns\CreatesSecurityMocks;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @covers  \App\Security\Voter\NotificationVoter
 * Class NotificationVoter
 * @package App\Tests\Security
 */
class NotificationVoterTest extends TestCase implements Actionable
{
	use CreatesSecurityMocks;
	
	/**
	 * @dataProvider provideConstruct
	 * @param  null|User  $user
	 */
	public function test__construct(?User $user)
	{
		$security = $this->createSecurity();
		
		$security
			->expects(static::once())
			->method('getUser')
			->willReturn($user)
		;
		
		new NotificationVoter($security);
	}
	
	public function provideConstruct()
	{
		yield 'voter construct with a user' => [
			$this->createUser(1),
		];
		
		yield 'voter construct without a user' => [
			null,
		];
	}
	
	/**
	 * @dataProvider provideSupports
	 * @param  string  $attribute
	 * @param          $subject
	 * @param          $expected
	 */
	public function testSupports(string $attribute, $subject, bool $expected)
	{
		$security = $this->createSecurity();
		
		$security
			->expects(static::once())
			->method('getUser')
			->willReturn($this->createUser(1))
		;
		
		$voter = new NotificationVoter($security);
		
		static::assertSame(
			$expected,
			$voter->supports($attribute, $subject)
		);
	}
	
	public function provideSupports()
	{
		yield 'voter supports view on Notification' => [
			self::VIEW,
			new Notification(),
			true,
		];
		
		yield 'voter supports edit on Notification' => [
			self::EDIT,
			new Notification(),
			true,
		];
		
		yield 'voter supports delete on Notification' => [
			self::DELETE,
			new Notification(),
			true,
		];
		
		yield 'voter does not support view on NotificationVoterTest' => [
			self::VIEW,
			new NotificationVoterTest(),
			false,
		];
		
		yield 'voter does not support edit on NotificationVoterTest' => [
			self::EDIT,
			new NotificationVoterTest(),
			false,
		];
		
		yield 'voter does not support delete on NotificationVoterTest' => [
			self::DELETE,
			new NotificationVoterTest(),
			false,
		];
	}
	
	/**
	 * @dataProvider provideCases
	 * @param  string        $attribute
	 * @param  Notification  $notification
	 * @param  null|User     $user
	 * @param  int           $expected
	 * @param  bool          $adminTest
	 */
	public function testVoteOnAttribute(string $attribute, Notification $notification, ?User $user, int $expected, bool $adminTest)
	{
		$security = $this->createSecurity();
		
		$security
			->expects(static::once())
			->method('getUser')
			->willReturn($user)
		;
		
		if (true === $adminTest) {
			
			$security
				->expects(static::once())
				->method('isGranted')
				->with(User::ROLE_ADMINISTRATOR)
				->willReturn(true)
			;
		}
		
		$voter = new NotificationVoter($security);
		
		// If we pass in a user, create a token with fake data otherwise anonymous token.
		if (! is_null($user)) {
			
			$token = new UsernamePasswordToken($user, 'credentials', 'memory');
		} else {
			
			$token = new AnonymousToken('secret', 'anonymous');
		}
		
		static::assertSame(
			$expected,
			$voter->vote($token, $notification, [$attribute])
		);
	}
	
	public function provideCases()
	{
		yield 'anonymous cannot see notification' => [
			self::VIEW,
			(new Notification())->setAuthor($this->createUser(1)),
			null,
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'anonymous cannot edit notification' => [
			self::EDIT,
			(new Notification())->setAuthor($this->createUser(1)),
			null,
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'anonymous cannot delete notification' => [
			self::DELETE,
			(new Notification())->setAuthor($this->createUser(1)),
			null,
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'non-owner can not see notification' => [
			self::VIEW,
			(new Notification())->setAuthor($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'non-owner can not edit notification' => [
			self::EDIT,
			(new Notification())->setAuthor($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'non-owner can not delete notification' => [
			self::DELETE,
			(new Notification())->setAuthor($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'owner can see notification' => [
			self::VIEW,
			(new Notification())->setAuthor($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
		];
		
		yield 'owner can edit notification' => [
			self::EDIT,
			(new Notification())->setAuthor($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
		];
		
		yield 'owner can delete notification' => [
			self::DELETE,
			(new Notification())->setAuthor($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
		];
		
		yield 'admin can see notification' => [
			self::VIEW,
			(new Notification())->setAuthor($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
		];
		
		yield 'admin can edit notification' => [
			self::EDIT,
			(new Notification())->setAuthor($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
		];
		
		yield 'admin can delete notification' => [
			self::DELETE,
			(new Notification())->setAuthor($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
		];
	}
}