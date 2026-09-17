<?php

namespace App\Core;

/**
 * Event Dispatcher (Observer Pattern)
 */
class EventDispatcher
{
    private static ?EventDispatcher $instance = null;
    private array $listeners = [];

    public static function getInstance(): EventDispatcher
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register an event listener callback
     */
    public function listen(string $event, callable $handler): void
    {
        $this->listeners[$event][] = $handler;
    }

    /**
     * Dispatch an event to all registered listeners
     */
    public function dispatch(string $event, mixed $payload = null): void
    {
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $handler) {
                call_user_func($handler, $payload);
            }
        }
    }
}
