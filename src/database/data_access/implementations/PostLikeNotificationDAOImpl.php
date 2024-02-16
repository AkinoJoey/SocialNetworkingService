<?php

namespace src\database\data_access\implementations;

use DateTime;
use src\database\data_access\interfaces\PostLikeNotificationDAO;
use src\models\PostLikeNotification;
use src\database\DatabaseManager;

class PostLikeNotificationDAOImpl implements PostLikeNotificationDAO
{
    public function create(PostLikeNotification $postLikeNotification): bool
    {
        if ($postLikeNotification->getId() !== null) throw new \Exception('Cannot create a post like notification with an existing ID. id: ' . $postLikeNotification->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO post_like_notifications (user_id, post_id, is_read) VALUES (?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'iii',
            [
                $postLikeNotification->getUserId(),
                $postLikeNotification->getPostId(),
                $postLikeNotification->getIsRead()
            ]
        );

        if (!$result) return false;

        $postLikeNotification->setId($mysqli->insert_id);

        return true;
    }

    public function getById(int $id): ?PostLikeNotification
    {
        $postLikeNotificationRaw = $this->getRawById($id);
        if ($postLikeNotificationRaw === null) return null;

        return $this->rawDataToPostLikeNotification($postLikeNotificationRaw);
    }

    private function getRawById(int $id): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM post_like_notifications WHERE id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$id])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    private function rawDataToPostLikeNotification(array $rawData): PostLikeNotification
    {
        return new PostLikeNotification(
            userId: $rawData['user_id'],
            postId: $rawData['post_id'],
            isRead: $rawData['is_read'],
            id: $rawData['id'],
            createdAt: new DateTime($rawData['created_at'])
        );
    }

    public function update(PostLikeNotification $postLikeNotification): bool
    {
        if ($postLikeNotification->getId() === null) throw new \Exception('Post like notification specified has no ID.');

        $current = $this->getById($postLikeNotification->getId());
        if ($current === null) throw new \Exception(sprintf("Post like notification %s does not exist.", $postLikeNotification->getId()));

        $mysqli = DatabaseManager::getMysqliConnection();

        $query =
            <<<SQL
            UPDATE post_like_notifications
                SET 
                    user_id = ?,
                    post_id = ?,
                    is_read = ?,
                WHERE id = ?
            SQL;

        $result = $mysqli->prepareAndExecute(
            $query,
            'iii',
            [
                $postLikeNotification->getUserId(),
                $postLikeNotification->getPostId(),
                $postLikeNotification->getIsRead(),
                $postLikeNotification->getId()
            ]
        );

        if (!$result) return false;

        return true;
    }

    public function delete(int $userId, int $postId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM post_like_notifications WHERE user_id = ? AND post_id = ?", 'ii', [$userId, $postId]);
    }
}
