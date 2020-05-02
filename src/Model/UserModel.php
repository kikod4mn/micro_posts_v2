<?php

declare(strict_types = 1);

namespace App\Model;

use App\Model\Abstracts\AbstractModel;
use App\Model\Concerns\DecodesUuid;
use App\Model\Concerns\WorksWithUuid;
use App\Repository\UserRepository;

class UserModel extends AbstractModel
{
	use DecodesUuid, WorksWithUuid;
	
	/**
	 * UserModel constructor.
	 * @param  UserRepository  $repository
	 */
	public function __construct(UserRepository $repository)
	{
		parent::__construct($repository);
	}
}