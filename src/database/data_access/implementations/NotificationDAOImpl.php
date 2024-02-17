<?php

namespace src\database\data_access\implementations;

use DateTime;
use src\database\data_access\interfaces\NotificationDAO;
use src\models\Notification;
use src\database\DatabaseManager;

class NotificationDAOImpl implements NotificationDAO
{
    public function create(Notification $notification): bool
    {
        if ($notification->getId() !== null) throw new \Exception('Cannot create a notification with an existing ID. id: ' . $notification->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO notifications (user_id, notification_type, related_id, is_read) VALUES (?, ?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'isii',
            [
                $notification->getUserId(),
                $notification->getNotificationType(),
                $notification->getRelatedId(),
                $notification->getIsRead()
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
            notificationType: $rawData['notification_type'],
            relatedId: $rawData['related_id'],
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
                    notification_type = ?,
                    related_id = ?,
                    is_read = ?,
                WHERE id = ?
            SQL;

        $result = $mysqli->prepareAndExecute(
            $query,
            'isiii',
            [
                $notification->getUserId(),
                $notification->getNotificationType(),
                $notification->getRelatedId(),
                $notification->getIsRead(),
                $notification->getId()
            ]
        );

        if (!$result) return false;

        return true;
    }

    public function delete(int $userId, string $notificationType, int $relatedId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM notifications WHERE user_id = ? AND notification_type = ? AND related_id = ?", 'isi', [$userId, $notificationType, $relatedId]);
    }

    public function getNotificationList(int $userId, int $limit = 100): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at LIMIT ?";

        $results = $mysqli->prepareAndFetchAll($query, 'ii', [$userId, $limit]);

        return $results === null ? [] : $this->rawDataToNotifications($results);
    }
}
