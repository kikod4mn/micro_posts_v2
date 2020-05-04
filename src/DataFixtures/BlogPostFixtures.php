<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BlogPostFixtures extends BaseFixture implements DependentFixtureInterface
{
	/**
	 * @inheritDoc
	 */
	protected function loadData(ObjectManager $manager): void
	{
		$faker = Factory::create();
		$this->createMany(
			BlogPost::class, 250, function (BlogPost $blogPost, $i) use ($faker) {
			$blogPost->setTitle($faker->realText(60));
			$blogPost->setBody($faker->realText(10000));
			$blogPost->setAuthor($this->getRandomReference(User::class));
			$blogPost->setPublishedAt(new \DateTime());
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
