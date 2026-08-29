<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks;

final class AreaProvider
{
	public function __construct(
		private AreaRegistry $registry
	) {
	}

	public function register(array $areas): void
	{
		foreach ($areas as $page => $pageAreas) {
			foreach ($pageAreas as $area => $label) {
				$this->registry->register(
					$page,
					$area,
					$label
				);
			}
		}
	}
}
