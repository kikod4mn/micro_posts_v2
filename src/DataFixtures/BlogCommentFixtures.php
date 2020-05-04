<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\BlogComment;
use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BlogCommentFixtures extends BaseFixture implements DependentFixtureInterface
{
	/**
	 * @inheritDoc
	 */
	protected function loadData(ObjectManager $manager): void
	{
		$faker = Factory::create();
		$this->createMany(
			BlogComment::class, 750, function (BlogComment $blogComment, $i) use ($faker) {
			$blogComment->setBody($faker->realText(240));
			$blogComment->setAuthor($this->getRandomReference(User::class));
			$blogComment->setBlogPost($this->getRandomReference(BlogPost::class));
			$blogComment->setPublishedAt(new \DateTime());
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
			BlogPostFixtures::class,
		];
	}
}
