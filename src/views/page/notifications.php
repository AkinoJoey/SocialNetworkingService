<?php
use src\types\NotificationType;
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
                switch ($notification->getNotificationType()) {
                    case NotificationType::POST_LIKE->value:
                        $notificationMessage =  'さんがあなたの投稿にいいねしました';
                        $href = '/posts?url=' . $notification->getPostUrl();
                        break;
                    case NotificationType::COMMENT->value;
                        $notificationMessage =  'さんがあなたの投稿にコメントをしました';
                        $href = '/comments?url=' . $notification->getCommentUrl();
                        break;
                    case NotificationType::FOLLOW->value;
                        $notificationMessage =  'さんがあなたをフォローしました';
                        $href = '/profile?username=' . $notification->getUsername();
                        break;
                    case NotificationType::DM->value;
                        $notificationMessage =  'さんからあなたにメッセージが届いています';
                        $href = '/direct?url=' . $notification->getThreadUrl();
                        break;
                }

                ?>
                <a href="<?= $href ?>" data-notification-id="<?= $notification->getId() ?>" data-notification-isRead="<?= $notification->getIsRead() ?>" class="notification flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 hover:cursor-pointer">
                    <div class="indicator">
                        <div class="pr-4">
                            <?php if (!$notification->getIsRead()) : ?>
                                <span id="indicator" class="indicator-item indicator-middle indicator-start badge badge-primary badge-xs"></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex w-full items-center">
                            <div class="mr-2">
                                <img class="rounded-full w-16" src="https://source.unsplash.com/100x100/?portrait" alt="Jese image" />
                            </div>
                            <div class="w-3/4">
                                <div class="flex h-full flex-col justify-center">
                                    <div class="mb-1.5 text-sm <?php echo ($notification->getIsRead()) ? 'text-gray-500 dark:text-gray-400' : 'text-gray-950 dark:text-gray-400'  ?>">
                                        <span class="font-semibold   <?php echo ($notification->getIsRead()) ? 'text-gray-600 dark:text-white' : 'text-gray-900 dark:text-white' ?> "><?= $notification->getAccountName() ?></span>
                                        <?= $notificationMessage ?>
                                    </div>
                                    <div class="text-xs <?php echo ($notification->getIsRead()) ? 'text-gray-500 dark:text-gray-400' : 'text-blue-600 dark:text-blue-500' ?>">
                                        a few moments ago
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<script>
    const csrfToken = "<?= src\helpers\CrossSiteForgeryProtection::getToken(); ?>"
</script>
<script src="/notifications.bundle.js"></script>