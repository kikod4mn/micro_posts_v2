<?php

namespace App\Twig;

use App\Model\Doctrine\UuidEncoder;
use Ramsey\Uuid\UuidInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UuidExtension extends AbstractExtension
{
	/**
	 * @var UuidEncoder
	 */
	private $encoder;
	
	/**
	 * UuidExtension constructor.
	 * @param  UuidEncoder  $encoder
	 */
	public function __construct(UuidEncoder $encoder)
	{
		$this->encoder = $encoder;
	}
	
	/**
	 * @return array|TwigFunction[]
	 */
	public function getFunctions(): array
	{
		return [
			new TwigFunction(
				'uuid_encode',
				[$this, 'encodeUuid'],
				['is_safe' => ['html']]
			),
		];
	}
	
	/**
	 * @param  UuidInterface  $uuid
	 * @return string
	 */
	public function encodeUuid(UuidInterface $uuid): string
	{
		return $this->encoder->encode($uuid);
	}
}