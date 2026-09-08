<?php
declare(strict_types=1);

namespace Monoverse\Entities;

class Edition
{
public function __construct(
public readonly string $id,
public readonly string $name,
public readonly string $description,
public readonly string $version,
public readonly string $author = '',
public readonly string $website = '',
public readonly string $icon = '',
public readonly array $modules = [],
public readonly string $status = 'available'
) {
}

public function isAvailable(): bool
{
return $this->status === 'available';
}
}
