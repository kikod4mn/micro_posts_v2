<?php

declare(strict_types = 1);

namespace App\Service;

use App\Repository\BlogPostCommentRepository;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class StatisticsService
{
	private $statisticsHaveBeenSent;
	
	/**
	 * @var MicroPostRepository
	 */
	private $postRepository;
	
	/**
	 * @var BlogPostCommentRepository
	 */
	private $commentRepository;
	
	/**
	 * @var MailSender
	 */
	private $mailSender;
	
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	
	/**
	 * @var array
	 */
	private $weeklyMostViewedPosts;
	
	/**
	 * @var array
	 */
	private $allTimeMostViewedPosts;
	
	/**
	 * @var array
	 */
	private $weeklyTopPosts;
	
	/**
	 * @var array
	 */
	private $allTimeTopPosts;
	
	/**
	 * @var array
	 */
	private $weeklyTopComments;
	
	/**
	 * @var array
	 */
	private $allTimeTopComments;
	
	/**
	 * @var string
	 */
	private $mailTo;
	
	/**
	 * StatisticsService constructor.
	 * @param  MicroPostRepository        $postRepository
	 * @param  BlogPostCommentRepository  $commentRepository
	 * @param  MailSender                 $mailSender
	 * @param  EntityManagerInterface     $entityManager
	 * @param  string                     $mailTo
	 */
	public function __construct(
		MicroPostRepository $postRepository,
		BlogPostCommentRepository $commentRepository,
		MailSender $mailSender,
		EntityManagerInterface $entityManager,
		string $mailTo
	)
	{
		$this->statisticsHaveBeenSent = false;
		$this->postRepository         = $postRepository;
		$this->commentRepository      = $commentRepository;
		$this->mailSender             = $mailSender;
		$this->entityManager          = $entityManager;
		$this->mailTo                 = $mailTo;
	}
	
	/**
	 * Send the weekly statistics email to the site administrator and handle the reset of weekly statistics.
	 * @throws TransportExceptionInterface
	 */
	public function sendWeeklyStatistics()
	{
		$this->weeklyTopPosts         = $this->postRepository->findBy([], ['weeklyLikeCount' => 'DESC'], 10);
		$this->allTimeTopPosts        = $this->postRepository->findBy([], ['likeCount' => 'DESC'], 10);
		$this->weeklyMostViewedPosts  = $this->postRepository->findBy([], ['weeklyViewCount' => 'DESC'], 10);
		$this->allTimeMostViewedPosts = $this->postRepository->findBy([], ['viewCount' => 'DESC'], 10);
		$this->weeklyTopComments      = $this->commentRepository->findBy([], ['weeklyLikeCount' => 'DESC'], 10);
		$this->allTimeTopComments     = $this->commentRepository->findBy([], ['likeCount' => 'DESC'], 10);
		$this->saveWeeklyStatistics();
		
		$this->mailSender->sendTwigEmail(
			$this->mailTo,
			'Weekly statistics for MicroPost',
			'email-templates/statistics.html.twig',
			[
				'weeklyTopPosts'         => $this->weeklyTopPosts,
				'allTimeTopPosts'        => $this->allTimeTopPosts,
				'weeklyMostViewedPosts'  => $this->weeklyMostViewedPosts,
				'allTimeMostViewedPosts' => $this->allTimeMostViewedPosts,
				'weeklyTopComments'      => $this->weeklyTopComments,
				'allTimeTopComments'     => $this->allTimeTopComments,
			]
		);
		
		$this->statisticsHaveBeenSent = true;
		
		$this->resetWeeklyStatistics();
		
		$this->statisticsHaveBeenSent = false;
	}
	
	/**
	 * Reset the counters for all articles in the database for the weekly statistics.
	 * If $statisticsHaveBeenSent bool is not set to true, do not allow the reset.
	 * Keeping it private to make sure no other class can set it but StatisticsService itself and only should set it
	 * after the statistics have been sent and weekly counts are no longer needed.
	 * @return void
	 */
	private function resetWeeklyStatistics(): void
	{
		if (!$this->statisticsHaveBeenSent) {
			return;
		}
		
		$posts = $this->postRepository->findAll();
		foreach ($posts as $post) {
			$post->resetWeeklyViewCount();
			$post->resetWeeklyLikeCounter();
		}
		
		$comments = $this->commentRepository->findAll();
		foreach ($comments as $comment) {
			$comment->resetWeeklyViewCount();
			$comment->resetWeeklyLikeCounter();
		}
	}
	
	/**
	 * todo write statistics to a file for later retrieval in admin interface.
	 * @return void
	 */
	private function saveWeeklyStatistics(): void
	{
	
	}
}