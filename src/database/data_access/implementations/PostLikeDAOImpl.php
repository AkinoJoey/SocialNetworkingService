<?php

namespace src\database\data_access\implementations;

use src\database\data_access\interfaces\PostLikeDAO;
use src\models\PostLike;
use src\database\DatabaseManager;
use DateTime;

class PostLikeDAOImpl implements PostLikeDAO
{

    public function create(PostLike $postLike): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO post_likes (user_id, post_id) VALUES (?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'ii',
            [
                $postLike->getUserId(),
                $postLike->getPostId(),
            ]
        );

        if (!$result) return false;

        return true;
    }

    public function getByUserIdAndPostId(int $userId, int $postId): ?PostLike
    {
        $postLikeRaw = $this->getRawByUserIdAndPostId($userId, $postId);
        if ($postLikeRaw === null) return null;

        return $this->rawDataToPostLike($postLikeRaw);
    }

    private function getRawByUserIdAndPostId(int $userId, int $postId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM post_likes WHERE user_id = ? AND post_id = ? ";

        $result = $mysqli->prepareAndFetchAll($query, 'ii', [$userId, $postId])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    private function rawDataToPostLike(array $rawData): PostLike
    {
        return new PostLike(
            userId: $rawData['user_id'],
            postId: $rawData['post_id'],
            createdAt: new DateTime($rawData['created_at'])
        );
    }

    public function delete(int $userId, int $postId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM post_likes WHERE user_id = ? AND post_id = ?", 'ii', [$userId, $postId]);
    }

    public function getNumberOfLikes(int $postId): int
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT COUNT(*) FROM post_likes WHERE post_id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$postId])[0];

        return $result["COUNT(*)"];
    }
}
