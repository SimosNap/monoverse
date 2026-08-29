<?php
declare(strict_types=1);

namespace Monoverse\Core;

class Config
{
    private array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->items;
    
        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
    
            $value = $value[$segment];
        }
    
        return $value;
    }
}
