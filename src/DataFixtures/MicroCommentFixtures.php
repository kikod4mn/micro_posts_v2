<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\MicroComment;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MicroCommentFixtures extends BaseFixture implements DependentFixtureInterface
{
	/**
	 * @inheritDoc
	 */
	protected function loadData(ObjectManager $manager): void
	{
		$faker = Factory::create();
		$this->createMany(
			MicroComment::class, 750, function (MicroComment $microComment, $i) use ($faker) {
			$microComment->setBody($faker->realText(240));
			$microComment->setAuthor($this->getRandomReference(User::class));
			$microComment->setMicroPost($this->getRandomReference(MicroPost::class));
			$microComment->publish();
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
			MicroPostFixtures::class,
		];
	}
}
