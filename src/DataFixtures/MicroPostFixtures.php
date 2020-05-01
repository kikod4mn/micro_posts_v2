<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MicroPostFixtures extends BaseFixture implements DependentFixtureInterface
{
	/**
	 * @inheritDoc
	 */
	protected function loadData(ObjectManager $manager): void
	{
		$faker = Factory::create();
		$this->createMany(
			MicroPost::class, 250, function (MicroPost $microPost, $i) use ($faker) {
			$microPost->setBody($faker->realText(240));
			$microPost->setAuthor($this->getRandomReference(User::class));
			$microPost->publish();
		}
		);
		
		$manager->flush();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getDependencies()
	{
		return [
			UserFixtures::class,
		];
	}
}
