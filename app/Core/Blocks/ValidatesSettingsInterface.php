<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks;

interface ValidatesSettingsInterface
{
	public function validateSettings(
		array &$settings
	): array;
}