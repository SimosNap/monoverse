<?php
declare(strict_types=1);

namespace Monoverse\Core;

use RuntimeException;

class Container
{
    /**
     * @var array<string, callable>
     */
    private array $bindings = [];

    /**
     * @var array<string, mixed>
     */
    private array $instances = [];

    public function set(string $id, callable $resolver): void
    {
        $this->bindings[$id] = $resolver;
    }

    public function instance(string $id, mixed $instance): void
    {
        $this->instances[$id] = $instance;
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) || isset($this->instances[$id]);
    }

    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->bindings[$id])) {
            throw new RuntimeException(
                'Service not found in container: ' . $id
            );
        }

        $this->instances[$id] = $this->bindings[$id]($this);

        return $this->instances[$id];
    }
}
