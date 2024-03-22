<?php

namespace src\middleware;

use src\database\data_access\DAOFactory;
use src\helpers\Authenticate;
use src\helpers\ValidationHelper;
use src\response\FlashData;
use src\response\HTTPRenderer;
use src\response\render\RedirectRenderer;
use src\routing\Route;

class SignatureValidationMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        $currentPath = $_SERVER['REQUEST_URI'] ?? '';
        $parsedUrl = parse_url($currentPath);
        $pathWithoutQuery = $parsedUrl['path'] ?? '';

        // 現在のパスのRouteオブジェクトを作成します。
        $route = Route::create($pathWithoutQuery, function () {
        });

        // URLに有効な署名があるかチェックします。
        if ($route->isSignedURLValid($_SERVER['HTTP_HOST'] . $currentPath)) {
            // 有効期限があるかどうかを確認し、有効期限がある場合は有効期限が切れていないことを確認します。
            if (isset($_GET['expiration']) && ValidationHelper::integer($_GET['expiration']) < time()) {
                $userDao = DAOFactory::getUserDAO();

                // サインアップ後に期限切れの場合はログアウトさせて、データベースからユーザーを削除
                if ($pathWithoutQuery === '/verify/email') {
                    $user = Authenticate::getAuthenticatedUser();
                    if($user !== null){
                        Authenticate::logoutUser();
                    }
                    $userDao->delete($user->getId());
                }

                if($pathWithoutQuery === '/verify/forgot_password'){
                    $userDao->deleteExpiredEmailVerificationUsers();
                }
                
                FlashData::setFlashData('error', "URLの期限が切れています");
                return new RedirectRenderer('login');
            }

            // 署名が有効であれば、ミドルウェアチェインを進めます。
            return $next();
        } else {
            // 署名が有効でない場合、ログインページににリダイレクトします。
            FlashData::setFlashData('error', sprintf("無効なURL (%s).", $pathWithoutQuery));
            return new RedirectRenderer('login');
        }
    }
}
