<?php

namespace src\middleware;

use src\response\HTTPRenderer;

interface Middleware
{
    public function handle(callable $next): HTTPRenderer;
}
