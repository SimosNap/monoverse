<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks;

final class AreaRegistry
{
	/**
	 * @var array<string, array<string, string>>
	 */
	private array $areas = [];

	public function register(
		string $page,
		string $area,
		string $label
	): void {
		$this->areas[$page][$area] = $label;
	}

	public function all(): array
	{
		return $this->areas;
	}

	public function page(string $page): array
	{
		return $this->areas[$page] ?? [];
	}

	public function exists(
		string $page,
		string $area
	): bool {
		return isset($this->areas[$page][$area]);
	}
}
