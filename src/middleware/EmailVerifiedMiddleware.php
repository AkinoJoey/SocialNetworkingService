<?php

namespace src\middleware;

use src\helpers\Authenticate;
use src\response\FlashData;
use src\response\HTTPRenderer;
use src\response\render\RedirectRenderer;

class EmailVerifiedMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        error_log('Running verification check...');
        if (!Authenticate::isEmailVerified()) {
            FlashData::setFlashData('error', 'Eメール認証が必要なページです');
            return new RedirectRenderer('');
        }

        return $next();
    }
}
