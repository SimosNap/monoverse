<?php

declare(strict_types=1);

namespace Monoverse\Helpers;

final class NumberHelper
{
	public static function bytes(?int $bytes): string
	{
		if ($bytes === null || $bytes <= 0) {
			return '0 B';
		}

		$units = ['B', 'KB', 'MB', 'GB', 'TB'];

		$power = min(
			(int) floor(log($bytes, 1024)),
			count($units) - 1
		);

		return sprintf(
			$power === 0 ? '%d %s' : '%.1f %s',
			$bytes / (1024 ** $power),
			$units[$power]
		);
	}
}
