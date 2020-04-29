<?php

namespace App\Model\Concerns;

use App\Model\Doctrine\UuidEncoder;

trait DecodesUuid
{
	/**
	 * @var UuidEncoder
	 */
	protected $uuidEncoder = null;
	
	/**
	 * @param  string  $encoded
	 * @return string
	 */
	protected function decodeUuid(string $encoded): string
	{
		if (! $this->isInitialized()) {
			
			$this->init();
		}
		
		return $this->uuidEncoder->decode($encoded);
	}
	
	/**
	 * @return bool
	 */
	protected function isInitialized(): bool
	{
		return ! is_null($this->uuidEncoder);
	}
	
	/**
	 * Init $uuidEncoder
	 */
	protected function init(): void
	{
		$this->uuidEncoder = new UuidEncoder();
	}
}