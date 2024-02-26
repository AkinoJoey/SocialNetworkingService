<?php

namespace src\database\data_access\implementations;

use DateTime;
use src\database\data_access\interfaces\NotificationDAO;
use src\models\Notification;
use src\database\DatabaseManager;
use src\types\NotificationType;

class NotificationDAOImpl implements NotificationDAO
{
    public function create(Notification $notification): bool
    {
        if ($notification->getId() !== null) throw new \Exception('Cannot create a notification with an existing ID. id: ' . $notification->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO notifications (user_id, source_id, notification_type, post_id, comment_id,  dm_thread_id) VALUES (?, ?, ?, ?,?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'iisiii',
            [
                $notification->getUserId(),
                $notification->getSourceId(),
                $notification->getNotificationType(),
                $notification->getPostId(),
                $notification->getCommentId(),
                $notification->getDmThreadId(),
            ]
        );

        if (!$result) return false;

        $notification->setId($mysqli->insert_id);

        return true;
    }

    public function getById(int $id): ?Notification
    {
        $notificationRaw = $this->getRawById($id);
        if ($notificationRaw === null) return null;

        return $this->rawDataToNotification($notificationRaw);
    }

    private function getRawById(int $id): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM notifications WHERE id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$id])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    private function rawDataToNotification(array $rawData): Notification
    {
        return new Notification(
            userId: $rawData['user_id'],
            sourceId: $rawData['source_id'],
            notificationType: $rawData['notification_type'],
            postId: $rawData['post_id'],
            commentId: $rawData['comment_id'],
            dmThreadId: $rawData['dm_thread_id'],
            isRead: $rawData['is_read'],
            id: $rawData['id'],
            createdAt: new DateTime($rawData['created_at'])
        );
    }

    private function rawDataToNotifications(array $results): array
    {
        $notifications = [];

        foreach ($results as $result) {
            $notifications[] = $this->rawDataToNotification($result);
        }

        return $notifications;
    }

    public function update(Notification $notification): bool
    {
        if ($notification->getId() === null) throw new \Exception('Notification specified has no ID.');

        $current = $this->getById($notification->getId());
        if ($current === null) throw new \Exception(sprintf("Notification %s does not exist.", $notification->getId()));

        $mysqli = DatabaseManager::getMysqliConnection();

        $query =
            <<<SQL
            UPDATE notifications
                SET 
                    user_id = ?,
                    source_id = ?,
                    notification_type = ?,
                    post_id = ?,
                    comment_id = ?
                    dm_thread_id = ?,
                    is_read = ?,
                WHERE id = ?
            SQL;

        $result = $mysqli->prepareAndExecute(
            $query,
            'iisiiiii',
            [
                $notification->getUserId(),
                $notification->getNotificationType(),
                $notification->getPostId(),
                $notification->getCommentId(),
                $notification->getDmThreadId(),
                $notification->getIsRead(),
                $notification->getId()
            ]
        );

        if (!$result) return false;

        return true;
    }

    public function updateReadStatus(int $id, bool $status = true): bool
    {
        $mysql = DatabaseManager::getMysqliConnection();

        $query = "UPDATE notifications SET is_read = ? WHERE id = ?";
        $result = $mysql->prepareAndExecute(
            $query,
            'is',
            [$status, $id]
        );

        if (!$result) return false;

        return true;
    }

    public function delete(int $userId, string $notificationType, int $sourceId, ?int $postId, ?int $commentId, ?int $dmThreadId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        if ($notificationType === NotificationType::FOLLOW->value) {
            return $mysqli->prepareAndExecute("DELETE FROM notifications WHERE (user_id = ? AND notification_type = ? AND source_id = ?)", 'isi', [$userId, $notificationType, $sourceId]);
        } else {
            return $mysqli->prepareAndExecute("DELETE FROM notifications WHERE (user_id = ? AND notification_type = ? AND source_id = ?) OR (post_id = ? OR comment_id = ? OR dm_thread_id = ?)", 'isiiii', [$userId, $notificationType, $sourceId, $postId, $commentId, $dmThreadId]);
        }
    }

    public function getNotificationList(int $userId, int $limit = 100): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";

        $results = $mysqli->prepareAndFetchAll($query, 'ii', [$userId, $limit]);

        return $results === null ? [] : $this->rawDataToNotifications($results);
    }

    public function getNumberOfNotification(int $userId): int
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT COUNT(*) AS number_of_notification FROM notifications WHERE user_id = ? AND is_read = 0";

        $results = $mysqli->prepareAndFetchAll($query, 'i', [$userId])[0];

        return $results['number_of_notification'];
    }
}
