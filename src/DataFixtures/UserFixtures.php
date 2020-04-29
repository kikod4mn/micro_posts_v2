<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserPreferences;
use App\Entity\UserProfile;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixture
{
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $passwordEncoder;
	
	/**
	 * @var string
	 */
	private $defaultLocale;
	
	/**
	 * UserFixtures constructor.
	 * @param  UserPasswordEncoderInterface  $passwordEncoder
	 * @param  string                        $defaultLocale
	 */
	public function __construct(UserPasswordEncoderInterface $passwordEncoder, string $defaultLocale)
	{
		$this->passwordEncoder = $passwordEncoder;
		$this->defaultLocale = $defaultLocale;
	}
	
	/**
	 * @inheritDoc
	 */
	protected function loadData(ObjectManager $manager): void
	{
		$user  = new User();
		$faker = Factory::create();
		$this->createMany(
			User::class, 50, function (User $user, $i) use ($faker) {
			$user->setUsername($faker->userName);
			$user->setFullname($faker->name);
			$user->setEmail($faker->email);
			$user->setPassword($this->passwordEncoder->encodePassword($user, 'secret'));
			$user->activate();
			
			$preferences = new UserPreferences();
			$profile     = new UserProfile();
			$preferences->setLocale($this->defaultLocale);
			$user->setPreferences($preferences);
			$user->setProfile($profile);
			$profile->setUser($user);
			$profile->setAvatar('images/defaultUserAvatar/defaultAvatar.jpg');
		}
		);
		
		$manager->flush();
	}
}
