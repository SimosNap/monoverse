<?php
declare(strict_types=1);

namespace Monoverse\Installer;

class Edition
{
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly string $description,
        public readonly string $version,
        public readonly string $status = 'available'
    ) {
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}

