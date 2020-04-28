<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Model\UserModel;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	/**
	 * @Route("/", name="homepage")
	 */
	public function index(UserModel $model, PostRepository $repository): Response
	{
		//		$test = $model->findMany([12, 5, 6]);
		//		/** @var User $item */
		//		foreach ($test as $item) {
		//			$item->trash();
		//		}
		
		/** @var Post $post */
		$post = $repository->findOneBy(['id' => 1]);
		
		$post->report($model->find(21));
		
		dump($post->getReportedBy());
		die;
		
		/** @var User $user */
		$user = $model->getTrashed();
		
		//		$user->trash();
		
		dump($user);
		
		die;
		
		return $this->render('home/index.html.twig', ['user' => $user->toJson(0, ['default', 'user-with-posts', 'user-with-comments'])]);
	}
}