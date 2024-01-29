<?php

namespace src\middleware;

use src\response\HTTPRenderer;

class SessionsSetupMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        error_log('Setting up sessions...');
        session_start();
        // セッションに関するその他の処理を行います

        // 次のミドルウェアに進みます
        return $next();
    }
}
