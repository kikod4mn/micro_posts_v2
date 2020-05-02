<?php

declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogCommentController extends AbstractController
{
	/**
	 * @return Response
	 */
	public function index(): Response
	{
		return $this->render('');
	}
	
	/**
	 * @return Response
	 */
	public function create(): Response
	{
		return $this->render('');
	}
	
	/**
	 * @param  Request  $request
	 * @return Response
	 */
	public function store(Request $request): Response
	{
		return $this->redirectToRoute('');
	}
	
	/**
	 * @param  string  $id
	 * @return Response
	 */
	public function show(string $id): Response
	{
		return $this->render('');
	}
	
	/**
	 * @param  string  $id
	 * @return Response
	 */
	public function edit(string $id): Response
	{
		return $this->render('');
	}
	
	/**
	 * @param  Request  $request
	 * @param  string   $id
	 * @return Response
	 */
	public function update(Request $request, string $id): Response
	{
		return $this->redirectToRoute('');
	}
	
	/**
	 * @param  string  $id
	 * @return Response
	 */
	public function destroy(string $id): Response
	{
		return $this->render('');
	}
}