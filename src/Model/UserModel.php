<?php

declare(strict_types = 1);

namespace App\Model;

use App\Model\Abstracts\AbstractModel;
use App\Repository\UserRepository;

class UserModel extends AbstractModel
{
	public function __construct(UserRepository $repository)
	{
		parent::__construct($repository);
	}
}