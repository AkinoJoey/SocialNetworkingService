<?php

namespace src\database\data_access\implementations;

use src\database\data_access\interfaces\CommentLikeDAO;
use src\models\CommentLike;
use src\database\DatabaseManager;
use DateTime;

class CommentLikeDAOImpl implements CommentLikeDAO
{

    public function create(CommentLike $commentLike): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO comment_likes (user_id, comment_id) VALUES (?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'ii',
            [
                $commentLike->getUserId(),
                $commentLike->getCommentId(),
            ]
        );

        if (!$result) return false;

        return true;
    }

    public function getByUserIdAndPostId(int $userId, int $commentId): ?CommentLike
    {
        $commentLikeRaw = $this->getRawByUserIdAndPostId($userId, $commentId);
        if ($commentLikeRaw === null) return null;

        return $this->rawDataToCommentLike($commentLikeRaw);
    }

    private function getRawByUserIdAndPostId(int $userId, int $commentId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM comment_likes WHERE user_id = ? AND comment_id = ? ";

        $result = $mysqli->prepareAndFetchAll($query, 'ii', [$userId, $commentId])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    private function rawDataToCommentLike(array $rawData): CommentLike
    {
        return new CommentLike(
            userId: $rawData['user_id'],
            commentId: $rawData['comment_id'],
            createdAt: new DateTime($rawData['created_at'])
        );
    }

    public function delete(int $userId, int $commentId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM comment_likes WHERE user_id = ? AND comment_id = ?", 'ii', [$userId, $commentId]);
    }

    public function getNumberOfLikes(int $commentId): int
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$commentId])[0];

        return $result["COUNT(*)"];
    }
}
