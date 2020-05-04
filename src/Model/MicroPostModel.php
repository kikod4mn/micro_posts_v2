<?php

declare(strict_types = 1);

namespace App\Model;

use App\Model\Abstracts\AbstractModel;
use App\Model\Concerns\DecodesUuid;
use App\Model\Concerns\FindsSlugs;
use App\Model\Concerns\Paginates;
use App\Model\Concerns\FindsUuids;
use App\Model\Contracts\Filterable;
use App\Model\Contracts\Paginatable;
use App\Repository\MicroPostRepository;
use App\Support\Contracts\FilterInterface;

class MicroPostModel extends AbstractModel implements Filterable, Paginatable
{
	use FindsUuids, DecodesUuid, FindsSlugs, Paginates;
	
	/**
	 * MicroPostModel constructor.
	 * @param  MicroPostRepository  $repository
	 * @param  FilterInterface      $filter
	 */
	public function __construct(MicroPostRepository $repository, FilterInterface $filter)
	{
		parent::__construct($repository, $filter);
	}
	
	/**
	 * @return string|array
	 */
	public function findForIndex()
	{
		return $this->findBy([], [], 10);
	}
	
}