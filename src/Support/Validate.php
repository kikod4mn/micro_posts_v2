<?php

declare(strict_types = 1);

namespace App\Support;

final class Validate
{
	public static function isSet($var)
	{
		return isset($var) || ! empty($var);
	}
	
	public static function isNumeric($var): bool
	{
		return is_integer($var) || is_numeric($var) && ! is_string($var);
	}
	
	public static function isFileOk(string $file)
	{
		return file_exists($file)
			&& ! is_dir($file)
			&& is_readable($file)
			&& is_writeable($file);
	}
	

}