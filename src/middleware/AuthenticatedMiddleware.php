<?php

namespace src\middleware;

use src\helpers\Authenticate;
use src\response\FlashData;
use src\response\HTTPRenderer;
use src\response\render\RedirectRenderer;

class AuthenticatedMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        error_log('Running authentication check...');
        if (!Authenticate::isLoggedIn()) {
            FlashData::setFlashData('error', 'ログインが必要なページです');
            return new RedirectRenderer('login');
        }

        return $next();
    }
}
