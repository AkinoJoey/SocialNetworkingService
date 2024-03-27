<?php

namespace src\helpers;

use src\database\data_access\DAOFactory;
use src\models\DmMessage;
use src\models\Notification;
use src\types\NotificationType;

class ChatHelper
{
    public static function createMessage(string $message, int $senderUserId, int $receiverUserId, int $dmThreadId): void
    {
        // 3行以上の改行は2行にする
        $encryptedData = CipherHelper::encryptMessage(preg_replace("/(\R{3,})/", "\n\n", $message));

        $messageDao = DAOFactory::getDmMessageDAO();
        $dmMessage = new DmMessage(
            message: $encryptedData['encrypted'],
            iv: $encryptedData['iv'],
            senderUserId: $senderUserId,
            receiverUserId: $receiverUserId,
            dmThreadId: $dmThreadId,
        );

        $success = $messageDao->create($dmMessage);
        if (!$success) throw new \Exception('メッセージの作成に失敗しました');
    }

    public static function createOrUpdateNotification(int $receiverUserId, int $senderUserId, int $dmThreadId): void
    {
        $notificationDao = DAOFactory::getNotificationDAO();
        $notificationId =  $notificationDao->getUnreadDMNotificationId($receiverUserId, $senderUserId);

        if (isset($notificationId)) {
            $success = $notificationDao->updateUpdatedAt($notificationId);
            if (!$success) throw new \Exception('通知の更新に失敗しました');
        } else {

            $notification = new Notification(
                id: $notificationId,
                userId: $receiverUserId,
                sourceId: $senderUserId,
                notificationType: NotificationType::DM->value,
                dmThreadId: $dmThreadId,
            );

            $success = $notificationDao->create($notification);
            if (!$success) throw new \Exception('通知の作成に失敗しました');
        }
    }
}
