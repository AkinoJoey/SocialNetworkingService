<?php

namespace src\database\data_access\implementations;

use src\models\DataTimeStamp;
use src\database\data_access\interfaces\CommentDAO;
use src\database\DatabaseManager;
use src\models\Comment;

class CommentDAOImpl implements CommentDAO
{
    public function create(Comment $comment): bool
    {
        if ($comment->getId() !== null) throw new \Exception('Cannot create a comment with an existing ID. id: ' . $comment->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO comments (content, url ,media_path, extension, user_id, post_id, parent_comment_id) VALUES (?, ? ,?, ?, ?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'ssssiii',
            [
                $comment->getContent() !== null ? preg_replace("/(\R{3,})/", "\n\n", $comment->getContent()) : null,
                $comment->getUrl(),
                $comment->getMediaPath(),
                $comment->getExtension(),
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

    private function getRawByUrl(string $url, int $userId): ?array
    {
        $commentId = $this->getCommentIdByUrl($url);

        $mysqli = DatabaseManager::getMysqliConnection();

        $query =
            <<<SQL
            WITH comment_data AS(
                SELECT c.*, u.account_name , u.username
                FROM comments c 
                    LEFT JOIN users u ON c.user_id = u.id
                    WHERE c.id = ?
            ),
            like_data AS(
                SELECT cl.comment_id, count(cl.comment_id) AS number_of_likes
                FROM comment_likes cl 
                WHERE cl.comment_id = ?
                GROUP BY cl.comment_id
            ),
            user_likes AS (
                SELECT cl.comment_id, COUNT(*) AS is_like
                FROM comment_likes cl
                WHERE cl.user_id = ? AND cl.comment_id = ?
                GROUP BY cl.comment_id
            )
            SELECT cd.*, COALESCE(ld.number_of_likes, 0) AS number_of_likes, COALESCE(ul.is_like, 0) AS is_like
                FROM comment_data cd
                LEFT JOIN like_data ld ON cd.id = ld.comment_id
                LEFT JOIN user_likes ul ON cd.id = ul.comment_id;
            SQL;


        $result = $mysqli->prepareAndFetchAll($query, 'iiii', [$commentId, $commentId, $userId, $commentId])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    private function getCommentIdByUrl(string $url): ?int
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT c.id FROM comments c WHERE url = ?";

        $result = $mysqli->prepareAndFetchAll($query, 's', [$url])[0] ?? null;

        if ($result === null) return null;

        return $result['id'];
    }

    public function getById(int $id): ?Comment
    {
        $commentRaw = $this->getRawById($id);
        if ($commentRaw === null) return null;

        return $this->rawDataToComment($commentRaw);
    }

    public function getByUrl(string $url, int $userId): ?Comment
    {
        $commentRow = $this->getRawByUrl($url, $userId);
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
            extension: $rawData['extension'],
            timeStamp: new DataTimeStamp($rawData['created_at'], $rawData['updated_at']),
            username: $rawData['username'] ?? null,
            accountName: $rawData['account_name'] ?? null,
            numberOfComments: $rawData['number_of_comments'] ?? null,
            numberOfLikes: $rawData['number_of_likes'] ?? null,
            isLike: $rawData['is_like'] ?? null,
            profileImagePath: $rawData['profile_image_path'] ?? null,
            profileImageExtension: $rawData['profile_image_extension'] ?? null
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

    public function delete(int $id, int $userId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM comments WHERE id = ? AND user_id = ?", 'ii', [$id, $userId]);
    }

    public function getChildComments(int $parentCommentId, int $userId, int $offset, int $limit = 20): array
    {
        $childCommentIds = $this->getChildCommentIdsByParentCommentId($parentCommentId);
        if ($childCommentIds === null) return [];

        $mysqli = DatabaseManager::getMysqliConnection();
        $placeholders = implode(',', array_fill(0, count($childCommentIds), '?'));

        $query =
            <<<SQL
            WITH comments_data AS(
                SELECT c.*, u.account_name, u.username
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.parent_comment_id = ?
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
            ),user_likes AS (
                SELECT cl.comment_id, COUNT(*) AS is_like
                FROM comment_likes cl
                WHERE cl.user_id = ? AND cl.comment_id IN ($placeholders)
                GROUP BY cl.comment_id
            )
            select cd.*, p.profile_image_path, p.extension AS profile_image_extension, COALESCE(ns.number_of_comments, 0) AS number_of_comments, COALESCE(nl.number_of_likes, 0) AS number_of_likes, COALESCE(ul.is_like, 0) as is_like
                from comments_data cd
                LEFT JOIN number_of_comments ns ON cd.id = ns.parent_comment_id
                LEFT JOIN number_of_likes nl ON cd.id = nl.comment_id
                LEFT JOIN user_likes ul ON cd.id = ul.comment_id
                LEFT JOIN profiles p on cd.user_id = p.user_id
                WHERE cd.parent_comment_id = ? LIMIT ?, ?;
            SQL;

        $commentIdsTypes = str_repeat('i', count($childCommentIds));
        $types = $commentIdsTypes . $commentIdsTypes . $commentIdsTypes . 'iiiii';
        $params = array_merge([$parentCommentId], $childCommentIds, $childCommentIds, [$userId], $childCommentIds, [$parentCommentId, $offset, $limit]);

        $results = $mysqli->prepareAndFetchAll($query, $types, $params);

        return $results === null ? [] : $this->rawDataToComments($results);
    }

    public function getCommentsToPost(int $postId, int $userId, int $offset, int $limit = 20): array
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
            ),user_likes AS (
                SELECT cl.comment_id, COUNT(*) AS is_like
                FROM comment_likes cl
                WHERE cl.user_id = ? AND cl.comment_id IN ($placeholders)
                GROUP BY cl.comment_id
            )
            select cd.*, p.profile_image_path, p.extension AS profile_image_extension, COALESCE(ns.number_of_comments, 0) AS number_of_comments, COALESCE(nl.number_of_likes, 0) AS number_of_likes, COALESCE(ul.is_like, 0) as is_like
                from comments_data cd
                LEFT JOIN number_of_comments ns ON cd.id = ns.parent_comment_id
                LEFT JOIN number_of_likes nl ON cd.id = nl.comment_id
                LEFT JOIN user_likes ul ON cd.id = ul.comment_id
                LEFT JOIN profiles p ON cd.user_id = p.user_id
                WHERE cd.post_id = ? LIMIT ?, ?;
            SQL;

        $commentIdsTypes = str_repeat('i', count($commentIds));
        $types = $commentIdsTypes . $commentIdsTypes . $commentIdsTypes . 'iiiii';
        $params = array_merge([$postId], $commentIds, $commentIds, [$userId],  $commentIds, [$postId, $offset, $limit]);

        $results = $mysqli->prepareAndFetchAll($query, $types, $params);


        return $results === null ? [] : $this->rawDataToComments($results);
    }

    private function getCommentIdsByPostId(int $postId, int $limit = 20): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT c.id FROM comments c WHERE c.post_id = ? LIMIT ?";

        $results = $mysqli->prepareAndFetchAll($query, 'ii', [$postId,  $limit]);

        if (count($results) == 0) return null;

        $userIds = [];
        foreach ($results as $row) {
            $userIds[] = $row['id'];
        }

        return $userIds;
    }

    private function getChildCommentIdsByParentCommentId(int $parentCommentId, int $limit = 20): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT c.id FROM comments c WHERE c.parent_comment_id = ? LIMIT ?";

        $results = $mysqli->prepareAndFetchAll($query, 'ii', [$parentCommentId,  $limit]);

        if (count($results) == 0) return null;

        $userIds = [];
        foreach ($results as $row) {
            $userIds[] = $row['id'];
        }

        return $userIds;
    }

    public function createForProto(int $counter, string $executeAt, Comment $comment): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $eventName = "random_comment_" . $counter;
        $content = mysqli_escape_string($mysqli, $comment->getContent());
        $url = mysqli_escape_string($mysqli, $comment->getUrl());
        $userId = $comment->getUserId();
        $postId = mysqli_escape_string($mysqli, $comment->getPostId());

        $query = <<<SQL
        CREATE EVENT IF NOT EXISTS $eventName
        ON SCHEDULE AT '$executeAt'
        DO
            INSERT INTO comments (content, url , user_id, postId) values('$content', '$url', $userId, $postId);
        SQL;

        $result = $mysqli->query($query);

        if (!$result) throw new \Exception('イベントの作成に失敗しました');

        return true;
    }

    public function deleteEvent(string $eventName): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "DROP EVENT IF EXISTS $eventName";
        $result = $mysqli->query($query);

        if (!$result) throw new \Exception("イベントの削除に失敗しました");

        return $result;
    }
}
