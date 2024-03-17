<?php

namespace src\database\data_access\implementations;

use src\models\DataTimeStamp;
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
            timeStamp: new DataTimeStamp($rawData['created_at'], $rawData['updated_at']),
            accountName: $rawData['account_name'] ?? null,
            username: $rawData['username'] ?? null,
            commentUrl: $rawData['comment_url'] ?? null,
            postUrl: $rawData['post_url'] ?? null,
            threadUrl: $rawData['thread_url'] ?? null,
            profileImagePath: $rawData['profile_image_path'] ?? null,
            profileImageExtension: $rawData['extension'] ?? null
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
                    comment_id = ?,
                    dm_thread_id = ?,
                    is_read = ?
                WHERE id = ?
            SQL;

        $result = $mysqli->prepareAndExecute(
            $query,
            'iisiiiii',
            [
                $notification->getUserId(),
                $notification->getSourceId(),
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
        $query =
            <<<SQL
            WITH user_data AS(
                SELECT n.id, u.account_name , u.username , p.profile_image_path, p.extension
                    FROM notifications n
                    JOIN users u ON n.source_id  = u.id
                    LEFT JOIN profiles p ON n.source_id = p.user_id
                    WHERE n.user_id = ?
                ), comment_data AS(
                SELECT n.id, c.url
                    FROM notifications n 
                    JOIN comments c ON n.comment_id = c.id
                    where n.user_id = ?
                ), post_data AS(
                SELECT n.id, p.url
                    FROM notifications n 
                    JOIN posts p ON n.post_id = p.id 
                    WHERE n.user_id = ?
                ), thread_data AS(
                SELECT n.id,dt.url 
                    FROM notifications n 
                    JOIN dm_threads dt ON n.dm_thread_id = dt.id
                    WHERE user_id1 = ? OR user_id2 = ?
                )
                SELECT n.*, ud.account_name, ud.username, ud.profile_image_path, ud.extension, cd.url as comment_url, pd.url as post_url, td.url as thread_url
                    FROM notifications n 
                    LEFT JOIN user_data ud ON n.id = ud.id
                    LEFT JOIN comment_data cd ON n.id = cd.id
                    LEFT JOIN post_data pd ON n.id = pd.id
                    LEFT JOIN thread_data td ON n.id = td.id
                    WHERE n.user_id = ?
                    ORDER BY n.updated_at DESC LIMIT ?;
            SQL;

        $results = $mysqli->prepareAndFetchAll($query, 'iiiiiii', [$userId, $userId, $userId, $userId, $userId, $userId, $limit]);

        return $results === null ? [] : $this->rawDataToNotifications($results);
    }

    public function getNumberOfNotification(int $userId): int
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT COUNT(*) AS number_of_notification FROM notifications WHERE user_id = ? AND is_read = 0";

        $results = $mysqli->prepareAndFetchAll($query, 'i', [$userId])[0];

        return $results['number_of_notification'];
    }

    public function getUnreadDMNotificationId(int $userId, int $sourceId): ?int
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query =
            <<<SQL
            SELECT id
                FROM notifications
                WHERE user_id = ?
                AND source_id = ? 
                AND notification_type = 'dm' 
                AND is_read = 0
                limit 1;
            SQL;

        $results = $mysqli->prepareAndFetchAll($query, 'ii', [$userId, $sourceId])[0] ?? null;

        if ($results === null) return null;

        error_log($results['id']);

        return $results['id'];
    }

    public function updateUpdatedAt(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "UPDATE notifications SET updated_at = CURRENT_TIMESTAMP WHERE id = ?";

        $result = $mysqli->prepareAndExecute($query, 'i', [$id]);

        if (!$result) return false;

        return true;
    }
}
