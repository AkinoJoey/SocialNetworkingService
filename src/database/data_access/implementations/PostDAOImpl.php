<?php

namespace src\database\data_access\implementations;

use src\database\data_access\interfaces\PostDAO;
use src\database\DatabaseManager;
use src\models\DataTimeStamp;
use src\models\Post;
use DateTime;

class PostDAOImpl implements PostDAO
{
    public function create(Post $post): bool
    {
        if ($post->getId() !== null) throw new \Exception('Cannot create a post with an existing ID. id: ' . $post->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO posts (status, content, url ,media_path, extension, user_id, scheduled_at) VALUES (?, ?, ?,  ? ,?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'sssssis',
            [
                $post->getStatus(),
                preg_replace("/(\R{3,})/", "\n\n", $post->getContent()), //3行以上の改行は2行にする
                $post->getUrl(),
                $post->getMediaPath(),
                $post->getExtension(),
                $post->getUserId(),
                $post->getScheduledAt() ? $post->getScheduledAt()->format('Y-m-d H:i:s') : null,
            ]
        );

        if (!$result) return false;

        $post->setId($mysqli->insert_id);

        return true;
    }

    // ダミーでは日付をランダムにしたいから、created_atを追加
    public function createForDummy(Post $post): bool
    {
        if ($post->getId() !== null) throw new \Exception('Cannot create a post with an existing ID. id: ' . $post->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO posts (status, content, url ,media_path, extension, user_id, scheduled_at, created_at) VALUES (?, ?, ?,  ? ,?, ?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'sssssiss',
            [
                $post->getStatus(),
                preg_replace("/(\R{3,})/", "\n\n", $post->getContent()),
                $post->getUrl(),
                $post->getMediaPath(),
                $post->getExtension(),
                $post->getUserId(),
                $post->getScheduledAt() ? $post->getScheduledAt()->format('Y-m-d H:i:s') : null,
                $post->getTimeStamp()->getCreatedAt()
            ]
        );

        if (!$result) return false;

        $post->setId($mysqli->insert_id);

        return true;
    }
    private function getRawById(int $id): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM posts WHERE id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$id])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    private function getRawByUrl(string $url, int $userId): ?array
    {
        $postId = $this->getPostIdByUrl($url);
        if ($postId === null) return null;

        $mysqli = DatabaseManager::getMysqliConnection();

        $query =
            <<<SQL
            WITH post_data AS(
                SELECT p.*, u.account_name , u.username
                    FROM posts p
                    LEFT JOIN users u ON p.user_id  = u.id
                    WHERE p.id = ? AND p.status = 'public'
            ),
            number_of_comments AS(
                SELECT pc.post_id, COUNT(*) AS number_of_comments
                    FROM comments pc
                    WHERE pc.post_id = ?
                    GROUP BY pc.post_id
            ),
            number_of_likes AS(
                SELECT pl.post_id, COUNT(*) AS number_of_likes
                    FROM post_likes pl 
                    WHERE pl.post_id = ?
                    GROUP BY pl.post_id
            ),
            user_likes AS (
                SELECT pl.post_id, COUNT(*) AS is_like
                FROM post_likes pl
                WHERE pl.user_id = ? AND pl.post_id = ?
                GROUP BY pl.post_id
            )
            SELECT pd.*, COALESCE(noc.number_of_comments, 0) AS number_of_comments ,COALESCE(nol.number_of_likes, 0) AS number_of_likes, COALESCE(ul.is_like, 0) AS is_like,pr.profile_image_path, pr.extension AS profile_image_extension
                FROM post_data pd
                LEFT JOIN number_of_comments noc ON pd.id = noc.post_id
                LEFT JOIN number_of_likes nol ON pd.id = nol.post_id
                LEFT JOIN user_likes ul ON pd.id = ul.post_id
                LEFT JOIN profiles pr on pd.user_id = pr.user_id;
            SQL;

        $result = $mysqli->prepareAndFetchAll($query, 'iiiii', [$postId, $postId, $postId, $userId, $postId])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    public function getById(int $id): ?Post
    {
        $postRaw = $this->getRawById($id);
        if ($postRaw === null) return null;

        return $this->rawDataToPost($postRaw);
    }

    public function getByUserId(int $userId): ?Post
    {
        $postRow = $this->getRowByUserId($userId);
        if ($postRow === null) return null;

        return $this->rawDataToPost($postRow);
    }

    public function getByUrl(string $url, int $userId): ?Post
    {
        $postRow = $this->getRawByUrl($url, $userId);
        if ($postRow === null) return null;

        return $this->rawDataToPost($postRow);
    }


    private function getRowByUserId(int $userId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM posts WHERE user_id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$userId])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    private function getPostIdByUrl(string $url): ?int
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT p.id FROM posts p WHERE url = ?";

        $result = $mysqli->prepareAndFetchAll($query, 's', [$url])[0] ?? null;

        if ($result === null) return null;

        return $result['id'];
    }

    private function rawDataToPost(array $rawData): Post
    {
        return new Post(
            content: $rawData['content'],
            status: $rawData['status'],
            url: $rawData['url'],
            userId: $rawData['user_id'],
            id: $rawData['id'] ?? null,
            mediaPath: $rawData['media_path'],
            extension: $rawData['extension'],
            scheduledAt: isset($rawData['scheduled_at']) ? new DateTime($rawData['scheduled_at']) : null,
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

    private function rawDataToPosts(array $results): array
    {
        $posts = [];

        foreach ($results as $result) {
            $posts[] = $this->rawDataToPost($result);
        }

        return $posts;
    }


    public function delete(int $id, int $userId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM posts WHERE id = ? AND user_id = ?", 'ii', [$id, $userId]);
    }

    public function getTwentyPosts(int $userId, int $offset, int $limit = 20): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?";

        $results = $mysqli->prepareAndFetchAll($query, 'iii', [$userId, $offset, $limit]);

        return $results === null ? [] : $this->rawDataToPosts($results);
    }


    public function getPostsByFollowedUsers(array $followedUserIds, int $userId, int $offset, int $limit = 20): array
    {
        // 自分の投稿を表示させるために追加
        $followedUserIds[] = $userId;

        if (count($followedUserIds) === 0) return [];

        $mysqli = DatabaseManager::getMysqliConnection();

        $placeholders = implode(',', array_fill(0, count($followedUserIds), '?'));

        $query =
            <<<SQL
            WITH post_data AS (
                SELECT p.*,
                    u.account_name, u.username
                FROM posts p
                INNER JOIN users u ON p.user_id = u.id
                WHERE p.user_id IN ($placeholders) AND p.status = 'public'
            ),
            comment_data AS (
                SELECT pc.post_id, COUNT(*) AS number_of_comments
                FROM comments pc
                GROUP BY pc.post_id
            ),
            like_data AS (
                SELECT pl.post_id, COUNT(*) AS number_of_likes
                FROM post_likes pl
                GROUP BY pl.post_id
            ),
            user_likes AS (
                SELECT pl.post_id, COUNT(*) AS is_like
                FROM post_likes pl
                WHERE pl.user_id = ?
                GROUP BY pl.post_id
            )
            SELECT pd.id, pd.content, pd.url, pd.media_path, pd.extension, pd.status,  pd.created_at, pd.updated_at ,pd.user_id, pr.profile_image_path, pr.extension AS profile_image_extension ,pd.account_name, pd.username,
                COALESCE(cd.number_of_comments, 0) AS number_of_comments,
                COALESCE(ld.number_of_likes, 0) AS number_of_likes,
                COALESCE(ul.is_like, 0) AS is_like
            FROM post_data pd
            LEFT JOIN comment_data cd ON pd.id = cd.post_id
            LEFT JOIN like_data ld ON pd.id = ld.post_id
            LEFT JOIN user_likes ul ON pd.id = ul.post_id
            LEFT JOIN profiles pr on pd.user_id = pr.user_id
            ORDER BY pd.created_at DESC LIMIT ?, ?;
            SQL;

        $types = str_repeat('i', count($followedUserIds)) . 'iii';
        $params = array_merge($followedUserIds, [$userId, $offset, $limit]);

        $results = $mysqli->prepareAndFetchAll($query, $types, $params);

        return $results === null ? [] : $this->rawDataToPosts($results);
    }

    public function postScheduled(): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query =
            <<<SQL
            UPDATE posts 
                SET status = 'public' 
                WHERE status = 'scheduled' AND scheduled_at IS NOT NULL AND now() > scheduled_at;
            SQL;

        $result = $mysqli->prepareAndExecute($query, "", []);

        return $result;
    }

    public function getTrendPosts(int $userId, int $offset, int $limit = 20): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query =
            <<<SQL
            WITH post_data AS (
                SELECT p.*,
                    u.account_name, u.username
                FROM posts p
                INNER JOIN users u ON p.user_id = u.id
                WHERE  p.status = 'public'
            ),
            comment_data AS (
                SELECT pc.post_id, COUNT(*) AS number_of_comments
                FROM comments pc
                GROUP BY pc.post_id
            ),
            like_data AS (
                SELECT pl.post_id, COUNT(*) AS number_of_likes
                FROM post_likes pl
                GROUP BY pl.post_id
            ),
            user_likes AS (
                SELECT pl.post_id, COUNT(*) AS is_like
                FROM post_likes pl
                WHERE pl.user_id = ?
                GROUP BY pl.post_id
            )
            SELECT pd.id, pd.content, pd.url, pd.media_path, pd.extension, pd.status,  pd.created_at, pd.updated_at ,pd.user_id,
                pr.profile_image_path, pr.extension AS profile_image_extension ,pd.account_name, pd.username,
                COALESCE(cd.number_of_comments, 0) AS number_of_comments,
                COALESCE(ld.number_of_likes, 0) AS number_of_likes,
                COALESCE(ul.is_like, 0) AS is_like
            FROM post_data pd
            LEFT JOIN comment_data cd ON pd.id = cd.post_id
            LEFT JOIN like_data ld ON pd.id = ld.post_id
            LEFT JOIN user_likes ul ON pd.id = ul.post_id
            LEFT JOIN profiles pr ON pd.user_id = pr.user_id
            ORDER BY DATE(pd.created_at) DESC,
            number_of_likes DESC
            LIMIT ?, ?;
            SQL;

        $results = $mysqli->prepareAndFetchAll($query, "iii", [$userId, $offset, $limit]);

        return $results === null ? [] : $this->rawDataToPosts($results);
    }

    public function getTrendPostsForGuest(int $offset, int $limit = 20): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query =
            <<<SQL
            WITH post_data AS (
                SELECT p.*,
                    u.account_name, u.username
                FROM posts p
                INNER JOIN users u ON p.user_id = u.id
                WHERE  p.status = 'public'
            ),
            comment_data AS (
                SELECT pc.post_id, COUNT(*) AS number_of_comments
                FROM comments pc
                GROUP BY pc.post_id
            ),
            like_data AS (
                SELECT pl.post_id, COUNT(*) AS number_of_likes
                FROM post_likes pl
                GROUP BY pl.post_id
            )
            SELECT pd.id, pd.content, pd.url, pd.media_path, pd.extension, pd.status,  pd.created_at, pd.updated_at ,pd.user_id,
                pr.profile_image_path, pr.extension AS profile_image_extension ,pd.account_name, pd.username,
                COALESCE(cd.number_of_comments, 0) AS number_of_comments,
                COALESCE(ld.number_of_likes, 0) AS number_of_likes
            FROM post_data pd
            LEFT JOIN comment_data cd ON pd.id = cd.post_id
            LEFT JOIN like_data ld ON pd.id = ld.post_id
            LEFT JOIN profiles pr ON pd.user_id = pr.user_id
            ORDER BY DATE(pd.created_at) DESC,
            number_of_likes DESC
            LIMIT ?, ?;
            SQL;

        $results = $mysqli->prepareAndFetchAll($query, "ii", [$offset, $limit]);

        return $results === null ? [] : $this->rawDataToPosts($results);
    }

    public function getScheduledPosts(int $userId): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM posts WHERE status = 'scheduled' AND user_id = ?";

        $results = $mysqli->prepareAndFetchAll($query, 'i', [$userId]);
        return $results === null ? [] : $this->rawDataToPosts($results);
    }

    public function countScheduledPosts(int $userId): ?int
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT COUNT(*) AS number_of_scheduled_posts FROM posts WHERE status = 'scheduled' AND user_id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$userId])[0] ?? null;
        if ($result === null) return null;

        return $result['number_of_scheduled_posts'];
    }

    public function getPostsByUsername(string $username, int $userId, int $offset, int $limit = 20): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = <<<SQL
            WITH post_data AS (
                SELECT p.*,
                    u.account_name, u.username
                FROM posts p
                INNER JOIN users u ON p.user_id = u.id
                WHERE u.username = ? AND p.status = 'public'
            ),
            comment_data AS (
                SELECT pc.post_id, COUNT(*) AS number_of_comments
                FROM comments pc
                GROUP BY pc.post_id
            ),
            like_data AS (
                SELECT pl.post_id, COUNT(*) AS number_of_likes
                FROM post_likes pl
                GROUP BY pl.post_id
            ),
            user_likes AS (
                SELECT pl.post_id, COUNT(*) AS is_like
                FROM post_likes pl
                WHERE pl.user_id = ?
                GROUP BY pl.post_id
            )
            SELECT pd.id, pd.content, pd.url, pd.media_path, pd.extension, pd.status,  pd.created_at, pd.updated_at ,pd.user_id, pr.profile_image_path, pr.extension AS profile_image_extension ,pd.account_name, pd.username,
                COALESCE(cd.number_of_comments, 0) AS number_of_comments,
                COALESCE(ld.number_of_likes, 0) AS number_of_likes,
                COALESCE(ul.is_like, 0) AS is_like
            FROM post_data pd
            LEFT JOIN comment_data cd ON pd.id = cd.post_id
            LEFT JOIN like_data ld ON pd.id = ld.post_id
            LEFT JOIN user_likes ul ON pd.id = ul.post_id
            LEFT JOIN profiles pr on pd.user_id = pr.user_id
            ORDER BY pd.created_at DESC LIMIT ?, ?;
            SQL;

        $results = $mysqli->prepareAndFetchAll($query, "siii", [$username, $userId, $offset, $limit]);

        return $results === null ? [] : $this->rawDataToPosts($results);
    }
}
