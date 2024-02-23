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

        $query =
            <<<SQL
            WITH comment_data AS(
                SELECT c.*, u.account_name , u.username
                FROM comments c 
                    LEFT JOIN users u ON c.user_id = u.id
                    WHERE url = ?
            ),
            like_data AS(
                SELECT cl.comment_id, count(cl.comment_id) AS number_of_likes
                FROM comment_likes cl 
                GROUP BY cl.comment_id
            )
            SELECT cd.*, COALESCE(ld.number_of_likes, 0) AS number_of_likes
                FROM comment_data cd
                LEFT JOIN like_data ld ON cd.id = ld.comment_id;
            SQL;


        $result = $mysqli->prepareAndFetchAll($query, 's', [$url])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    private function getCommentIdsByUrl(string $url): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT c.id FROM comments c where url = ?";
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
            createdAt: new DateTime($rawData['created_at']),
            username: $rawData['username'] ?? null,
            accountName: $rawData['account_name'] ?? null,
            numberOfComments: $rawData['number_of_comments'] ?? null,
            numberOfLikes: $rawData['number_of_likes'] ?? null,
            isLike: $rawData['is_like'] ?? null
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
        $commentIds = $this->getCommentIdsByPostId($postId);
        if ($commentIds === null) return [];

        $mysqli = DatabaseManager::getMysqliConnection();
        $placeholders = implode(',', array_fill(0, count($commentIds), '?'));

        $query =
            <<<SQL
            WITH comments_data AS(
                SELECT c.*, u.account_name, u.username
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ?
            ),number_of_comments AS(
                SELECT c.parent_comment_id, COUNT(*) AS number_of_comments
                FROM comments c
                WHERE c.parent_comment_id IN ($placeholders)
                GROUP BY c.parent_comment_id
            ),number_of_likes AS(
                SELECT cl.comment_id, COUNT(*) AS number_of_likes
                FROM comment_likes cl
                WHERE cl.comment_id in ($placeholders)
                GROUP BY cl.comment_id
            )
            select cd.*, COALESCE(ns.number_of_comments, 0) AS number_of_comments, COALESCE(nl.number_of_likes, 0) AS number_of_likes
                from comments_data cd
                LEFT JOIN number_of_comments ns ON cd.id = ns.parent_comment_id
                LEFT JOIN number_of_likes nl ON cd.id = nl.comment_id
                WHERE cd.post_id = ? LIMIT ?, ?;
            SQL;

        $commentIdsTypes = str_repeat('i', count($commentIds));
        $types = $commentIdsTypes . $commentIdsTypes . 'iiii';
        $params = array_merge([$postId], $commentIds, $commentIds, [$postId, $offset, $limit]);

        $results = $mysqli->prepareAndFetchAll($query, $types, $params);

        return $results === null ? [] : $this->rawDataToComments($results);
    }

    private function getCommentIdsByPostId(int $postId, int $limit = 20): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT c.id FROM comments c WHERE c.post_id = ? LIMIT ?";

        $results = $mysqli->prepareAndFetchAll($query, 'ii', [$postId,  $limit]);

        if ($results == null) return [];

        $userIds = [];
        foreach ($results as $row) {
            $userIds[] = $row['id'];
        }

        return $userIds;
    }
}
