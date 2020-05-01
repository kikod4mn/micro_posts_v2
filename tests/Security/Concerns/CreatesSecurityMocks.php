<?php

declare(strict_types = 1);

namespace App\Tests\Security\Concerns;

use App\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Security;

trait CreatesSecurityMocks
{
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
	 * @return MockObject|User
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
	 * @return MockObject|Security
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