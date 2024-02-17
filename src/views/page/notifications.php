<?php
use src\types\NotificationType;
use src\database\data_access\DAOFactory;

$userDao = DAOFactory::getUserDAO();
?>
<!-- notification -->
<div class="container mx-auto mb-14 flex items-center justify-center p-4">
    <div class="w-full max-w-2xl divide-y divide-gray-100 rounded-lg bg-white shadow dark:divide-gray-700 dark:bg-gray-800 sm:m-12 sm:w-8/12 lg:mx-40">
        <div class="block rounded-t-lg bg-gray-50 px-4 py-2 text-center font-medium text-gray-700 dark:bg-gray-800 dark:text-white">
            通知
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($notifications as $notification) : ?>
                <a href="#" class="flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex w-full items-center">
                        <div class="mr-2">
                            <img class="rounded-full" src="https://source.unsplash.com/100x100/?portrait" alt="Jese image" />
                        </div>
                        <div class="w-3/4">
                            <div class="flex h-full flex-col justify-center">
                                <div class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold text-gray-900 dark:text-white"><?= $userDao->getById($notification->getUserId())->getAccountName() ?></span>
                                    <?php
                                        switch($notification->getNotificationType()){
                                            case NotificationType::POST_LIKE->value:
                                                echo 'さんがあなたの投稿にいいねしました';
                                                break;
                                            case NotificationType::COMMENT->value;
                                                echo 'さんがあなたの投稿にコメントをしました';
                                                break;
                                            case NotificationType::FOLLOW->value;
                                                echo 'さんがあなたをフォローしました';
                                                break;
                                            case NotificationType::DM->value;
                                                echo 'さんからあなたにメッセージが届いています';
                                                break;
                                        }
                                    ?>
                                    : "Hey, what's up? All set for the presentation?"
                                </div>
                                <div class="text-xs text-blue-600 dark:text-blue-500">
                                    a few moments ago
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>