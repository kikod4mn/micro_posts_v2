<?php

declare(strict_types = 1);

namespace App\Tests\Security;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Security\Voter\Contracts\Actionable;
use App\Security\Voter\PostVoter;
use App\Tests\Security\Concerns\CreatesSecurityMocks;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @covers  \App\Security\Voter\PostVoter
 * Class PostVoterTest
 * @package App\Tests\Security
 */
class PostVoterTest extends TestCase implements Actionable
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
		
		new PostVoter($security);
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
		
		$voter = new PostVoter($security);
		
		static::assertSame(
			$expected,
			$voter->supports($attribute, $subject)
		);
	}
	
	public function provideSupports()
	{
		yield 'voter supports view on Post' => [
			self::VIEW,
			new MicroPost(),
			true,
		];
		
		yield 'voter supports edit on Post' => [
			self::EDIT,
			new MicroPost(),
			true,
		];
		
		yield 'voter supports delete on Post' => [
			self::DELETE,
			new MicroPost(),
			true,
		];
		
		yield 'voter does not support view on PostVoterTest' => [
			self::VIEW,
			new PostVoterTest(),
			false,
		];
		
		yield 'voter does not support edit on PostVoterTest' => [
			self::EDIT,
			new PostVoterTest(),
			false,
		];
		
		yield 'voter does not support delete on PostVoterTest' => [
			self::DELETE,
			new PostVoterTest(),
			false,
		];
	}
	
	/**
	 * @dataProvider provideCases
	 * @param  string     $attribute
	 * @param  MicroPost  $post
	 * @param  null|User  $user
	 * @param  int        $expected
	 * @param  bool       $adminTest
	 * @param  bool       $isPublished
	 */
	public function testVoteOnAttribute(string $attribute, MicroPost $post, ?User $user, int $expected, bool $adminTest, bool $isPublished)
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
		
		if (true === $isPublished) {
			$post->publish();
		}
		
		$voter = new PostVoter($security);
		
		// If we pass in a user, create a token with fake data otherwise anonymous token.
		if (! is_null($user)) {
			
			$token = new UsernamePasswordToken($user, 'credentials', 'memory');
		} else {
			
			$token = new AnonymousToken('secret', 'anonymous');
		}
		
		static::assertSame(
			$expected,
			$voter->vote($token, $post, [$attribute])
		);
	}
	
	public function provideCases()
	{
		yield 'anonymous cannot see unpublished post' => [
			self::VIEW,
			(new MicroPost())->setAuthor($this->createUser(1)),
			null,
			Voter::ACCESS_DENIED,
			false,
			false,
		];
		
		yield 'anonymous cannot edit unpublished post' => [
			self::EDIT,
			(new MicroPost())->setAuthor($this->createUser(1)),
			null,
			Voter::ACCESS_DENIED,
			false,
			false,
		];
		
		yield 'anonymous cannot delete unpublished post' => [
			self::DELETE,
			(new MicroPost())->setAuthor($this->createUser(1)),
			null,
			Voter::ACCESS_DENIED,
			false,
			false,
		];
		
		yield 'anonymous can see published post' => [
			self::VIEW,
			(new MicroPost())->setAuthor($this->createUser(1)),
			null,
			Voter::ACCESS_GRANTED,
			false,
			true,
		];
		
		yield 'anonymous can not edit published post' => [
			self::EDIT,
			(new MicroPost())->setAuthor($this->createUser(1)),
			null,
			Voter::ACCESS_DENIED,
			false,
			true,
		];
		
		yield 'anonymous can not delete published post' => [
			self::DELETE,
			(new MicroPost())->setAuthor($this->createUser(1)),
			null,
			Voter::ACCESS_DENIED,
			false,
			true,
		];
		
		yield 'non-owner cannot see unpublished post' => [
			self::VIEW,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
			false,
		];
		
		yield 'non-owner cannot edit unpublished post' => [
			self::EDIT,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
			false,
		];
		
		yield 'non-owner cannot delete unpublished post' => [
			self::DELETE,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
			false,
		];
		
		yield 'non-owner can see published post' => [
			self::VIEW,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_GRANTED,
			false,
			true,
		];
		
		yield 'non-owner can not edit published post' => [
			self::EDIT,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
			true,
		];
		
		yield 'non-owner can not delete published post' => [
			self::DELETE,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(2),
			Voter::ACCESS_DENIED,
			false,
			true,
		];
		
		yield 'owner can see unpublished post' => [
			self::VIEW,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
			false,
		];
		
		yield 'owner can edit unpublished post' => [
			self::EDIT,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
			false,
		];
		
		yield 'owner can delete unpublished post' => [
			self::DELETE,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
			false,
		];
		
		yield 'owner can see published post' => [
			self::VIEW,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
			true,
		];
		
		yield 'owner can edit published post' => [
			self::EDIT,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
			true,
		];
		
		yield 'owner can delete published post' => [
			self::DELETE,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createUser(1),
			Voter::ACCESS_GRANTED,
			false,
			true,
		];
		
		yield 'admin can see unpublished post' => [
			self::VIEW,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
			false,
		];
		
		yield 'admin can edit unpublished post' => [
			self::EDIT,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
			false,
		];
		
		yield 'admin can delete unpublished post' => [
			self::DELETE,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
			false,
		];
		
		yield 'admin can see published post' => [
			self::VIEW,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
			true,
		];
		
		yield 'admin can edit published post' => [
			self::EDIT,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
			true,
		];
		
		yield 'admin can delete published post' => [
			self::DELETE,
			(new MicroPost())->setAuthor($this->createUser(1)),
			$this->createAdmin(1),
			Voter::ACCESS_GRANTED,
			true,
			true,
		];
	}
}