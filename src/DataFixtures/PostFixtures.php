<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends BaseFixture implements DependentFixtureInterface
{
	/**
	 * @inheritDoc
	 */
	protected function loadData(ObjectManager $manager): void
	{
		$post  = new Post();
		$faker = Factory::create();
		$this->createMany(
			Post::class, 250, function (Post $post, $i) use ($faker) {
			$post->setBody($faker->text(240));
			$post->setAuthor($this->getRandomReference(User::class));
			$post->publish();
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
