<?php

namespace src\middleware;

use src\response\HTTPRenderer;

class MiddlewareHandler
{
    /**
     * @param Middleware[] $middlewares
     */
    public function __construct(private array $middlewares)
    {
    }
    public function run(callable $action): HTTPRenderer
    {
        $middlewares = array_reverse($this->middlewares);

        foreach ($middlewares as $middleware) {
            $action = fn () => $middleware->handle($action);
        }

        return $action();
    }
}
