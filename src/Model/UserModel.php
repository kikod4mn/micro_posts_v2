<?php

declare(strict_types = 1);

namespace App\Model;

use App\Model\Abstracts\AbstractModel;
use App\Model\Concerns\DecodesUuid;
use App\Model\Concerns\FindsUuids;
use App\Repository\UserRepository;
use App\Support\Contracts\FilterInterface;

class UserModel extends AbstractModel
{
	use DecodesUuid, FindsUuids;
	
	/**
	 * UserModel constructor.
	 * @param  UserRepository   $repository
	 * @param  FilterInterface  $filter
	 */
	public function __construct(UserRepository $repository, FilterInterface $filter)
	{
		parent::__construct($repository, $filter);
	}
}