<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\ContentCount;
use App\Entity\MicroPost;
use App\Model\MicroPostModel;
use App\Repository\ContentCountsRepository;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MicroPostController extends AbstractController
{
	/**
	 * @var MicroPostModel
	 */
	private $model;
	
	/**
	 * @var MicroPostRepository
	 */
	private $repository;
	
	/**
	 * @var ContentCount
	 */
	private $counts;
	
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	
	/**
	 * MicroPostController constructor.
	 * @param  MicroPostModel           $model
	 * @param  MicroPostRepository      $repository
	 * @param  ContentCountsRepository  $counts
	 * @param  EntityManagerInterface   $entityManager
	 */
	public function __construct(MicroPostModel $model, MicroPostRepository $repository, ContentCountsRepository $counts, EntityManagerInterface $entityManager)
	{
		$this->model         = $model;
		$this->repository    = $repository;
		$this->counts        = $counts;
		$this->entityManager = $entityManager;
	}
	
	/**
	 * Display the latest MicroPosts
	 * @return Response
	 */
	public function index(): Response
	{
		return $this->render(
			'micro_posts/index.html.twig',
			[
				//				'posts' => $this->repository->findBy([], ['id' => 'asc'], 15),
				//				'postsPHP' => $this->repository->findBy([], ['id' => 'desc'], 10)
				'postsPHP' => $this->model->groups(['post-list'])->findBy(['id' => [12, 11, 10, 23, 1, 41, 3]], [], 10),
				'posts'    => $this->model->groups(['post-list'])->asJson()->findForIndex(),
				//				'posts'    => ($this->model->getFreshInstance())->groups(['post-list'])->asJson()->findForIndex(),
			]
		);
	}
	
	/**
	 * @return Response
	 */
	public function create(): Response
	{
		return $this->render('micro_posts/create.html.twig');
	}
	
	/**
	 * @param  Request  $request
	 * @return Response
	 */
	public function store(Request $request): Response
	{
		$microPost = new MicroPost();
		
		return $this->redirectToRoute('micro_post_by_uuid', ['uuid' => $microPost->getUuid()]);
	}
	
	/**
	 * @param  string  $uuid
	 * @return Response
	 */
	public function showUuid(string $uuid): Response
	{
		return $this->render(
			'micro_posts/show.html.twig',
			[
				'post' => $this->model->asJson()->groups(['post-with-comments'])->find($id),
			]
		);
	}
	
	/**
	 * @param  string  $slug
	 * @return Response
	 */
	public function showSlug(string $slug): Response
	{
		return $this->render(
			'micro_posts/show.html.twig',
			[
				'post' => $this->model->asJson()->groups(['post-with-comments'])->find($id),
			]
		);
	}
	
	/**
	 * @param  string  $id
	 * @return Response
	 */
	public function edit(string $id): Response
	{
		return $this->render('micro_posts/edit.html.twig');
	}
	
	/**
	 * @param  Request  $request
	 * @param  string   $id
	 * @return Response
	 */
	public function update(Request $request, string $id): Response
	{
		$microPost = $this->model->findOrFailUuid($id);
		
		return $this->redirectToRoute('micro_post_by_slug', ['slug' => $microPost->getSlug()]);
	}
	
	/**
	 * @param  string  $id
	 * @return Response
	 */
	public function destroy(string $id): Response
	{
		return $this->redirectToRoute('micro_post_index');
	}
}