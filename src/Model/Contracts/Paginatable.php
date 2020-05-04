<?php

declare(strict_types = 1);

namespace App\Model\Contracts;

/**
 * Marker interface for paginatable models.
 */
interface Paginatable
{
	/**
	 * @return null|bool
	 */
	public function isPaginated(): ?bool;
	
	/**
	 * @param  bool  $isPaginated
	 * @return $this|Paginatable
	 */
	public function setIsPaginated(bool $isPaginated): Paginatable;
	
	/**
	 * @return int
	 */
	public function getPerPage(): int;
	
	/**
	 * @param  int  $perPage
	 * @return $this|Paginatable
	 */
	public function setPerPage(int $perPage): Paginatable;
	
	/**
	 * @return null|int
	 */
	public function getOffset(): ?int;
	
	/**
	 * @param  null|int  $offset
	 * @return $this|Paginatable
	 */
	public function setOffset(?int $offset): Paginatable;
	
	/**
	 * @return null|int
	 */
	public function getLimit(): ?int;
	
	/**
	 * @param  null|int  $limit
	 * @return $this|Paginatable
	 */
	public function setLimit(?int $limit): Paginatable;
	
	/**
	 * @return null|int
	 */
	public function hasOffset(): ?int;
	
	/**
	 * @return null|int
	 */
	public function hasLimit(): ?int;
	
	/**
	 * @param  int    $page
	 * @param  int    $limit
	 * @param  array  $options
	 * @return $this|Paginatable
	 */
	public function paginate(int $page = 1, int $limit = 10, array $options = []): Paginatable;
}