<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\MicroPost;
use App\Model\MicroPostModel;
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
	 * MicroPostController constructor.
	 * @param  MicroPostModel  $model
	 */
	public function __construct(MicroPostModel $model)
	{
		$this->model = $model;
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
				'posts' => $this->model->groups(['post-list'])->asJson()->findForIndex(),
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
		
		return $this->redirectToRoute('micro_post_by_slug', ['slug' => $microPost->getSlug()]);
	}
	
	/**
	 * @param  int  $id
	 * @return Response
	 */
	public function showId(int $id): Response
	{
		return $this->render('micro_posts/show.html.twig', ['post' => $this->model->findOrFail($id)->toJson(['post-with-comments'])]);
	}
	
	/**
	 * @param  string  $uuid
	 * @return Response
	 */
	public function showUuid(string $uuid): Response
	{
		return $this->render('micro_posts/show.html.twig', ['post' => $this->model->findOrFailUuid($uuid)->toJson(['post-with-comments'])]);
	}
	
	/**
	 * @param  string  $slug
	 * @return Response
	 */
	public function showSlug(string $slug): Response
	{
		return $this->render('micro_posts/show.html.twig', ['post' => $this->model->findOrFailSlug($slug)->toJson(['post-with-comments'])]);
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