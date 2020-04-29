<?php

declare(strict_types = 1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Security\Voter\Contracts\Actionable;
use App\Security\Voter\ProfileVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * @covers  \App\Security\Voter\ProfileVoter
 * Class ProfileVoterTest
 * @package App\Tests\Security
 */
class ProfileVoterTest extends TestCase implements Actionable
{
	/**
	 * @dataProvider provideSupports
	 * @param  string  $attribute
	 * @param          $subject
	 * @param          $expected
	 */
	public function testSupports(string $attribute, $subject, bool $expected)
	{
		$security = $this->createSecurity();
		
		$voter = new ProfileVoter($security);
		
		$this->assertSame(
			$expected,
			$voter->supports($attribute, $subject)
		);
	}
	
	public function provideSupports()
	{
		yield 'voter supports view on UserProfile' => [
			self::VIEW,
			new UserProfile(),
			true,
		];
		
		yield 'voter supports edit on UserProfile' => [
			self::EDIT,
			new UserProfile(),
			true,
		];
		
		yield 'voter supports delete on UserProfile' => [
			self::DELETE,
			new UserProfile(),
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
	 * @param  string       $attribute
	 * @param  UserProfile  $profile
	 * @param  null|User    $user
	 * @param               $expected
	 * @param  bool         $adminTest
	 */
	public function testVoteOnAttribute(string $attribute, UserProfile $profile, ?User $user, $expected, bool $adminTest)
	{
		$security = $this->createSecurity();
		
		$security
			->expects($this->once())
			->method('getUser')
			->willReturn($user)
		;
		
		if ($adminTest) {
			
			$security
				->expects($this->once())
				->method('isGranted')
				->with(User::ROLE_ADMINISTRATOR)
				->willReturn(true)
			;
		}
		
		$voter = new ProfileVoter($security);
		
		// If we pass in a user, create a token with fake data otherwise anonymous token.
		if (! is_null($user)) {
			
			$token = new UsernamePasswordToken($user, 'credentials', 'memory');
		} else {
			
			$token = new AnonymousToken('secret', 'anonymous');
		}
		
		$this->assertSame(
			$expected,
			$voter->vote($token, $profile, [$attribute])
		);
	}
	
	public function provideCases()
	{
		yield 'anonymous cannot see' => [
			self::VIEW,
			(new UserProfile())->setUser($this->createUser(1)),
			null,
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'anonymous cannot edit' => [
			self::EDIT,
			(new UserProfile())->setUser($this->createUser(1)),
			null,
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'anonymous cannot delete' => [
			self::DELETE,
			(new UserProfile())->setUser($this->createUser(1)),
			null,
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'non-owner cannot see' => [
			self::VIEW,
			(new UserProfile())->setUser($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'non-owner cannot edit' => [
			self::EDIT,
			(new UserProfile())->setUser($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'non-owner cannot delete' => [
			self::DELETE,
			(new UserProfile())->setUser($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
		];
		
		yield 'owner can see' => [
			self::VIEW,
			(new UserProfile())->setUser($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
		];
		
		yield 'owner can edit' => [
			self::EDIT,
			(new UserProfile())->setUser($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
		];
		
		yield 'owner can delete' => [
			self::DELETE,
			(new UserProfile())->setUser($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
		];
		
		yield 'admin can see' => [
			self::VIEW,
			(new UserProfile())->setUser($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
		];
		
		yield 'admin can edit' => [
			self::EDIT,
			(new UserProfile())->setUser($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
		];
		
		yield 'admin can delete' => [
			self::DELETE,
			(new UserProfile())->setUser($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
		];
	}
	
	protected function createUser(int $id): User
	{
		$user = $this
			->getMockBuilder(User::class)
			->setMethods(['getId'])
			->disableOriginalConstructor()
			->getMock()
		;
		
		$user->method('getId')->willReturn($id);
		
		return $user;
	}
	
	/**
	 * @param  int  $id
	 * @return \PHPUnit\Framework\MockObject\MockObject|User
	 */
	protected function createAdmin(int $id): User
	{
		$admin = $this
			->getMockBuilder(User::class)
			->setMethods(['getId'])
			->disableOriginalConstructor()
			->getMock()
		;
		
		// Make sure admin always returns the wrong id.
		$admin->method('getId')->willReturn($id + 1);
		
		return $admin;
	}
	
	/**
	 * @return \PHPUnit\Framework\MockObject\MockObject|Security
	 */
	protected function createSecurity(): Security
	{
		return $this
			->getMockBuilder(Security::class)
			->disableOriginalConstructor()
			->setMethods(['isGranted', 'getUser'])
			->getMock()
			;
	}
}