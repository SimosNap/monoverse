<?php
declare(strict_types=1);

namespace Monoverse\Core;

class EventDispatcher
{
    private array $listeners = [];

    public function listen(string $event, callable $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    public function dispatch(string $event, array &$context = []): void
    {
        if (empty($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $listener) {
            $listener($context);
        }
    }
}
