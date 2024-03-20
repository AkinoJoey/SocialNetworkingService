<?php

namespace src\middleware;

use src\helpers\Authenticate;
use src\response\HTTPRenderer;
use src\response\render\RedirectRenderer;

class RegisterMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        // ユーザーがログインしていて、Eメール認証が済んでいる場合はトップページにリダイレクトする
        if (Authenticate::isLoggedIn() && Authenticate::isEmailVerified()) {
            return new RedirectRenderer('');
        }

        //  ユーザーがログインしていない、もしくはログインしていてかつEメール認証がfalseの場合のみ次に進む
        return $next();
    }
}
