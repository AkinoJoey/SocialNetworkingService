<?php

namespace src\response\render;

use src\database\data_access\DAOFactory;
use src\response\HTTPRenderer;
use src\helpers\Authenticate;

class HTMLRenderer implements HTTPRenderer
{
    private string $viewFile;
    private array $data;

    public function __construct(string $viewFile, array $data = [])
    {
        $this->viewFile = $viewFile;
        $this->data = $data;
    }

    public function getFields(): array
    {
        return [
            'Content-Type' => 'text/html; charset=UTF-8',
        ];
    }

    public function getContent(): string
    {
        $viewPath = $this->getViewPath($this->viewFile);

        if (!file_exists($viewPath)) {
            throw new \Exception("View file {$viewPath} does not exist.");
        }

        // ob_startはすべての出力をバッファに取り込みます。
        // このバッファはob_get_cleanによって取得することができ、バッファの内容を返し、バッファをクリアします。
        ob_start();
        // extract関数は、キーを変数として現在のシンボルテーブルにインポートします
        extract($this->data);
        require $viewPath;
        return $this->getHeader() . ob_get_clean() . $this->getFooter();
    }

    private function getHeader(): string
    {
        ob_start();
        // guestの場合はサイドバーを表示しない
        $user = Authenticate::getAuthenticatedUser();

        if($user){
            $notificationDao = DAOFactory::getNotificationDAO();
            $numberOfNotification = $notificationDao->getNumberOfNotification($user->getId());
        }

        require $this->getViewPath('layout/sidebar');
        require $this->getViewPath('components/message-boxes');
        return ob_get_clean();
    }

    private function getFooter(): string
    {
        ob_start();
        // guestの場合はボトムナビゲーションを表示しない
        $user = Authenticate::getAuthenticatedUser();
        require $this->getViewPath('layout/footer');
        return ob_get_clean();
    }

    private function getViewPath(string $path): string
    {
        return sprintf("%s/%s/views/%s.php", __DIR__, '../..', $path);
    }
}
