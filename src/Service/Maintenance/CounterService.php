<?php

declare(strict_types = 1);

namespace App\Service\Maintenance;

use App\Entity\ContentCount;
use App\Repository\ContentCountsRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Count portal statistics and save to the db table content_counts.
 */
class CounterService
{
	/**
	 * @var ContentCountsRepository
	 */
	private $repository;
	
	/**
	 * @var ContentCount
	 */
	private $counter;
	
	/**
	 * @var EntityManagerInterface
	 */
	private $em;
	
	/**
	 * CounterService constructor.
	 * @param  ContentCountsRepository  $repository
	 * @param  EntityManagerInterface   $em
	 */
	public function __construct(ContentCountsRepository $repository, EntityManagerInterface $em)
	{
		$this->repository = $repository;
		$this->em         = $em;
	}
	
	/**
	 * Get all counts from repository and map to properties on a new object.
	 */
	public function mapCounts()
	{
		$counts = $this->repository->getCounts();
		
		$this->counter = new ContentCount();
		
		$this->counter->setPublicMicroPostCount($counts['microPost']);
		$this->counter->setPublicMicroCommentCount($counts['microComment']);
		$this->counter->setPublicBlogPostCount($counts['blogPost']);
		$this->counter->setPublicBlogCommentCount($counts['blogComment']);
		$this->counter->setPublicPictureCount($counts['picture']);
		$this->counter->setPublicGalleryCount($counts['gallery']);
	}
	
	/**
	 * Save the new entity to the database.
	 */
	public function save()
	{
		$this->em->persist($this->counter);
		
		$this->em->flush();
	}
}