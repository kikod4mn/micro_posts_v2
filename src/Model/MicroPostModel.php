<?php

declare(strict_types = 1);

namespace App\Model;

use App\Model\Abstracts\AbstractModel;
use App\Model\Concerns\DecodesUuid;
use App\Model\Concerns\FindsBySlug;
use App\Model\Concerns\WorksWithUuid;
use App\Repository\MicroPostRepository;

class MicroPostModel extends AbstractModel
{
	use WorksWithUuid, DecodesUuid, FindsBySlug;
	
	protected $filters = ['publishable:published', 'trashable:trashed'];
	
	/**
	 * MicroPostModel constructor.
	 * @param  MicroPostRepository  $repository
	 */
	public function __construct(MicroPostRepository $repository)
	{
		parent::__construct($repository);
	}
	
	/**
	 * @param  array|string[]  $groups
	 * @return string|array
	 */
	public function findForIndex(array $groups = [])
	{
		return $this->result($this->findBy([], ['createdAt' => 'DESC'], 10))->filters()->return();
	}
}