<?php

namespace src\middleware;

use src\response\FlashData;
use src\response\HTTPRenderer;
use src\response\render\RedirectRenderer;

class CSRFMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        // セッションにCSRFトークンが存在するかチェックします
        if (!isset($_SESSION['csrf_token'])) {
            // 32個のランダムバイトを生成し、16進数に変換してCSRFトークンとしてセッションに格納します
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $token = $_SESSION['csrf_token'];

        // 非GETリクエストのCSRFトークンをチェックします
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            if ($_POST['csrf_token'] !== $token) {
                FlashData::setFlashData('error', 'Access has been denied.');
                return new RedirectRenderer('');
            }
        }

        return $next();
    }
}
