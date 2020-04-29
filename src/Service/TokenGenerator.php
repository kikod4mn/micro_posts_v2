<?php

declare(strict_types = 1);

namespace App\Service;

use function random_int;

class TokenGenerator
{
	/**
	 * @var string
	 */
	private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	
	/**
	 * @param  int  $length
	 * @return string
	 * @throws \Exception
	 */
	public function getRandomSecureToken(int $length): string
	{
		$maxNumber = strlen(self::ALPHABET);
		$token     = '';
		
		for ($i = 0; $i < $length; $i++) {
			$token .= self::ALPHABET[random_int(0, $maxNumber - 1)];
		}
		
		return $token;
	}
}