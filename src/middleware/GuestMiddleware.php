<?php

namespace src\middleware;

use src\helpers\Authenticate;
use src\response\HTTPRenderer;
use src\response\render\RedirectRenderer;

class GuestMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        error_log('Running authentication check...');
        // ユーザーがログインしている場合は、メッセージなしでトップページにリダイレクトします
        if (Authenticate::isLoggedIn()) {
            return new RedirectRenderer('');
        }

        return $next();
    }
}
