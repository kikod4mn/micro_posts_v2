<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Model\UserModel;
use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	/**
	 * @Route("/", name="homepage")
	 */
	public function index(UserModel $model, MicroPostRepository $repository): Response
	{
		//		$test = $model->findMany([12, 5, 6]);
		//		/** @var User $item */
		//		foreach ($test as $item) {
		//			$item->trash();
		//		}
		
				/** @var MicroPost $post */
				$post = $repository->findOneBy(['id' => 1]);
		
//				$post->report($model->find(21));

//				dump($post->getWeeklyViewCount());
//				dump($post->getViewCount());
				die;
		//
		//		/** @var User $user */
		//		$user = $model->getTrashed();
		//
		//		//		$user->trash();
		//
		//		dump($user);
		//
		//		die;
		
		return $this->render('home/index.html.twig', []);
		//		return $this->render('home/index.html.twig', ['user' => $user->toJson(0, ['default', 'user-with-posts', 'user-with-comments'])]);
	}
}