<?php

declare(strict_types = 1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\Voter\AccountVoter;
use App\Security\Voter\Contracts\Actionable;
use App\Tests\Security\Concerns\CreatesSecurityMocks;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @covers  \App\Security\Voter\AccountVoter
 * Class AccountVoterTest
 * @package App\Tests\Security
 */
class AccountVoterTest extends TestCase implements Actionable
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
		
		new AccountVoter($security);
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
	 * @param  bool    $expected
	 */
	public function testSupports(string $attribute, $subject, bool $expected)
	{
		$security = $this->createSecurity();
		
		$security
			->expects(static::once())
			->method('getUser')
			->willReturn($this->createUser(1))
		;
		
		$voter = new AccountVoter($security);
		
		static::assertSame(
			$expected,
			$voter->supports($attribute, $subject)
		);
	}
	
	public function provideSupports()
	{
		yield 'voter supports view on User' => [
			self::VIEW,
			new User(),
			true,
		];
		
		yield 'voter supports edit on User' => [
			self::EDIT,
			new User(),
			true,
		];
		
		yield 'voter supports delete on User' => [
			self::DELETE,
			new User(),
			true,
		];
		
		yield 'voter does not support view on ProfileVoterTest' => [
			self::VIEW,
			new ProfileVoterTest(),
			false,
		];
		
		yield 'voter does not support edit on ProfileVoterTest' => [
			self::EDIT,
			new ProfileVoterTest(),
			false,
		];
		
		yield 'voter does not support delete on ProfileVoterTest' => [
			self::DELETE,
			new ProfileVoterTest(),
			false,
		];
	}
	
	/**
	 * @dataProvider provideCases
	 * @param  string     $attribute
	 * @param  User       $userAccount
	 * @param  null|User  $user
	 * @param  int        $expected
	 * @param  bool       $adminTest
	 */
	public function testVoteOnAttribute(string $attribute, User $userAccount, ?User $user, int $expected, bool $adminTest)
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
		
		$voter = new AccountVoter($security);
		
		// If we pass in a user, create a token with fake data otherwise anonymous token.
		if (! is_null($user)) {
			
			$token = new UsernamePasswordToken($user, 'credentials', 'memory');
		} else {
			
			$token = new AnonymousToken('secret', 'anonymous');
		}
		
		static::assertSame(
			$expected,
			$voter->vote($token, $userAccount, [$attribute])
		);
	}
	
	public function provideCases()
	{
		yield 'anonymous cannot see' => [
			self::VIEW,
			$this->createUser(1),
			null,
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'anonymous cannot edit' => [
			self::EDIT,
			$this->createUser(1),
			null,
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'anonymous cannot delete' => [
			self::DELETE,
			$this->createUser(1),
			null,
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'non-owner cannot see' => [
			self::VIEW,
			$this->createUser(1),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'non-owner cannot edit' => [
			self::EDIT,
			$this->createUser(1),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'non-owner cannot delete' => [
			self::DELETE,
			$this->createUser(1),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'owner can see' => [
			self::VIEW,
			$this->createUser(1),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
		];
		
		yield 'owner can edit' => [
			self::EDIT,
			$this->createUser(1),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
		];
		
		yield 'owner can delete' => [
			self::DELETE,
			$this->createUser(1),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
		];
		
		yield 'admin can see' => [
			self::VIEW,
			$this->createUser(1),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
		];
		
		yield 'admin can edit' => [
			self::EDIT,
			$this->createUser(1),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
		];
		
		yield 'admin can delete' => [
			self::DELETE,
			$this->createUser(1),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
		];
	}
}