<?php
declare(strict_types=1);

namespace Monoverse\Core;

class OAuthConfig
{
	public function __construct(
		private array $items
	) {
	}

	public function get(string $key, mixed $default = null): mixed
	{
		return $this->items[$key] ?? $default;
	}

	public function all(): array
	{
		return $this->items;
	}
}
