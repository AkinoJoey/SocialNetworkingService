<?php

namespace src\database\data_access\implementations;

use DateTime;
use src\database\data_access\interfaces\CommentDAO;
use src\database\DatabaseManager;
use src\models\Comment;

class CommentDAOImpl implements CommentDAO
{
    public function create(Comment $comment): bool
    {
        if ($comment->getId() !== null) throw new \Exception('Cannot create a comment with an existing ID. id: ' . $comment->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO comments (content, url ,media_path, user_id, post_id, parent_comment_id) VALUES (?, ? ,?, ?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'sssiii',
            [
                $comment->getContent(),
                $comment->getUrl(),
                $comment->getMediaPath(),
                $comment->getUserId(),
                $comment->getPostId(),
                $comment->getParentCommentId()
            ]
        );

        if (!$result) return false;

        $comment->setId($mysqli->insert_id);

        return true;
    }
    private function getRawById(int $id): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM comments WHERE id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$id])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    private function getRawByUrl(string $url): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM comments WHERE url = ?";

        $result = $mysqli->prepareAndFetchAll($query, 's', [$url])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    public function getById(int $id): ?Comment
    {
        $commentRaw = $this->getRawById($id);
        if ($commentRaw === null) return null;

        return $this->rawDataToComment($commentRaw);
    }

    public function getByUrl(string $url): ?Comment
    {
        $commentRow = $this->getRawByUrl($url);
        if ($commentRow === null) return null;

        return $this->rawDataToComment($commentRow);
    }

    private function rawDataToComment(array $rawData): Comment
    {
        return new Comment(
            content: $rawData['content'],
            url: $rawData['url'],
            userId: $rawData['user_id'],
            id: $rawData['id'],
            postId: $rawData['post_id'],
            parentCommentId: $rawData['parent_comment_id'],
            mediaPath: $rawData['media_path'],
            createdAt: new DateTime($rawData['created_at'])
        );
    }

    private function rawDataToComments(array $results): array
    {
        $comments = [];

        foreach ($results as $result) {
            $comments[] = $this->rawDataToComment($result);
        }

        return $comments;
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM comments WHERE id = ?", 'i', [$id]);
    }

    public function getChildComments(int $parentId, int $offset, int $limit = 20): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM comments WHERE parent_comment_id = ? ORDER BY created_at DESC LIMIT ?, ?";

        $results = $mysqli->prepareAndFetchAll($query, 'iii', [$parentId, $offset, $limit]);

        return $results === null ? [] : $this->rawDataToComments($results);
        
    }

    public function getCommentsToPost(int $postId, int $offset, int $limit = 20): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at LIMIT ?, ?";

        $results = $mysqli->prepareAndFetchAll($query, 'iii', [$postId, $offset, $limit]);

        return $results === null ? [] : $this->rawDataToComments($results);
    }
}
