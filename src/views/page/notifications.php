<?php

use src\types\NotificationType;
use src\database\data_access\DAOFactory;
?>
<!-- notification -->
<div class="container mx-auto mb-14 flex items-center justify-center p-4">
    <div class="w-full max-w-2xl divide-y divide-gray-100 rounded-lg bg-white shadow dark:divide-gray-700 dark:bg-gray-800 sm:m-12 sm:w-8/12 lg:mx-40">
        <div class="block rounded-t-lg bg-gray-50 px-4 py-2 text-center font-medium text-gray-700 dark:bg-gray-800 dark:text-white">
            通知
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($notifications as $notification) : ?>
                <?php
                $userDao = DAOFactory::getUserDAO();
                $sourceUser = $userDao->getById($notification->getSourceId());

                switch ($notification->getNotificationType()) {
                    case NotificationType::POST_LIKE->value:
                        $notificationMessage =  'さんがあなたの投稿にいいねしました';
                        $postDao = DAOFactory::getPostDAO();
                        $href = '/posts?url=' . $postDao->getById($notification->getPostId())->getUrl();
                        break;
                    case NotificationType::COMMENT->value;
                        $notificationMessage =  'さんがあなたの投稿にコメントをしました';
                        $commentDao = DAOFactory::getCommentDAO();
                        $href = '/comments?url=' . $commentDao->getById($notification->getCommentId())->getUrl();
                        break;
                    case NotificationType::FOLLOW->value;
                        $notificationMessage =  'さんがあなたをフォローしました';
                        $href = '/profile?username=' . $sourceUser->getUsername();
                        break;
                    case NotificationType::DM->value;
                        $notificationMessage =  'さんからあなたにメッセージが届いています';
                        $dmThreadDao = DAOFactory::getDmThreadDAO();
                        $href = '/direct?url=' . $dmThreadDao->getByUserIds($user->getId(), $notification->getSourceId());
                        break;
                }

                ?>
                <a href="<?= $href ?>" class="flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex w-full items-center">
                        <div class="mr-2">
                            <img class="rounded-full" src="https://source.unsplash.com/100x100/?portrait" alt="Jese image" />
                        </div>
                        <div class="w-3/4">
                            <div class="flex h-full flex-col justify-center">
                                <div class="mb-1.5 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold text-gray-900 dark:text-white"><?= $sourceUser->getAccountName() ?></span>
                                    <?= $notificationMessage ?>
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