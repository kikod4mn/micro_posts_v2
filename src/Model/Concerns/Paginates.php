<?php

declare(strict_types = 1);

namespace App\Model\Concerns;

use App\Model\Contracts\Paginatable;

/**
 * Pagination helpers for Models.
 */
trait Paginates
{
	/**
	 * @var int
	 */
	private $perPage = 15;
	
	/**
	 * @var null|int
	 */
	private $offset = null;
	
	/**
	 * @var null|int
	 */
	private $limit = null;
	
	/**
	 * @var bool
	 */
	private $isPaginated = false;
	
	/**
	 * @return null|bool
	 */
	public function isPaginated(): ?bool
	{
		return $this->isPaginated;
	}
	
	/**
	 * @param  bool  $isPaginated
	 * @return $this|Paginatable
	 */
	public function setIsPaginated(bool $isPaginated): Paginatable
	{
		$this->isPaginated = $isPaginated;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getPerPage(): int
	{
		return $this->perPage;
	}
	
	/**
	 * @param  int  $perPage
	 * @return $this|Paginatable
	 */
	public function setPerPage(int $perPage): Paginatable
	{
		$this->perPage = $perPage;
		
		return $this;
	}
	
	/**
	 * @return null|int
	 */
	public function getOffset(): ?int
	{
		return $this->offset;
	}
	
	/**
	 * @param  null|int  $offset
	 * @return $this|Paginatable
	 */
	public function setOffset(?int $offset): Paginatable
	{
		$this->offset = $offset;
		
		return $this;
	}
	
	/**
	 * @return null|int
	 */
	public function getLimit(): ?int
	{
		return $this->limit;
	}
	
	/**
	 * @param  null|int  $limit
	 * @return $this|Paginatable
	 */
	public function setLimit(?int $limit): Paginatable
	{
		$this->limit = $limit;
		
		return $this;
	}
	
	/**
	 * If the query requested is with pagination, will return paginated results offset to start at.
	 * @return null|int
	 */
	public function hasOffset(): ?int
	{
		return $this->isPaginated() ? $this->getOffset() : null;
	}
	
	/**
	 * Determine if the model has a limit for the current query.
	 * @return null|int
	 */
	public function hasLimit(): ?int
	{
		return $this->isPaginated() ? $this->getPerPage() : null;
	}
	
	/**
	 * @param  int    $page
	 * @param  int    $limit
	 * @param  array  $options
	 * @return $this|Paginatable
	 */
	public function paginate(int $page = 1, int $limit = 10, array $options = []): Paginatable
	{
		$this->setIsPaginated(true);
		
		$this->calculateOffset($page);
		$this->setLimit($limit);
		
		return $this;
	}
	
	/**
	 * Calculate the offset for pagination start by the page we wish to see.
	 * @param  int  $page
	 * @return int
	 */
	private function calculateOffset(int $page): int
	{
		return $this->getPerPage() * $page - $this->getPerPage();
	}
}