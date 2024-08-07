<?php

namespace src\database\data_access\implementations;

use src\database\data_access\interfaces\PostDAO;
use src\database\DatabaseManager;
use src\models\DataTimeStamp;
use src\models\Post;
use DateTime;
use mysqli;

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
                $post->getContent() !== null ? preg_replace("/(\R{3,})/", "\n\n", $post->getContent()) : null, //3行以上の改行は2行にする
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
            with post_data AS(
                SELECT p.*
                FROM posts p 
                WHERE p.user_id IN($placeholders) AND p.status = 'public'
                ORDER BY COALESCE(scheduled_at, created_at)  DESC, created_at DESC
                LIMIT ?, ?
            ),
            comment_counts AS (
                SELECT c.post_id, COUNT(*) AS number_of_comments
                FROM comments c
                WHERE c.post_id IN (SELECT id FROM post_data)
                GROUP BY c.post_id
            ),
            like_counts AS(
                SELECT pl.post_id, COUNT(*) AS number_of_likes
                FROM post_likes pl
                WHERE pl.post_id IN (SELECT id FROM post_data)
                GROUP BY pl.post_id
            ),
            user_data AS(
                SELECT u.id, u.account_name, u.username
                FROM users u
                WHERE u.id IN (SELECT post_data.user_id FROM post_data)
            ),
            profile_data AS(
                SELECT	pr.user_id, pr.profile_image_path, pr.extension AS profile_image_extension
                FROM profiles pr
                WHERE pr.user_id IN (SELECT post_data.user_id FROM post_data)
            ),
            user_likes AS (
                SELECT pl.post_id, pl.user_id, 1 AS is_like
                FROM post_likes pl
                WHERE pl.user_id = ? AND pl.post_id IN (SELECT id FROM post_data)
            )
            SELECT t.*, ud.username,ud.account_name ,
            pd.profile_image_path, pd.profile_image_extension ,
            COALESCE(cc.number_of_comments, 0) AS number_of_comments,
            COALESCE (lc.number_of_likes, 0) AS number_of_likes ,
            COALESCE(ul.is_like, 0)AS is_like
            FROM post_data t
            LEFT JOIN comment_counts cc ON t.id = cc.post_id
            LEFT JOIN like_counts lc ON t.id = lc.post_id
            LEFT JOIN user_data ud ON t.user_id = ud.id
            LEFT JOIN user_likes ul ON t.id = ul.post_id
            LEFT JOIN profile_data pd ON t.user_id = pd.user_id;
            SQL;

        $types = str_repeat('i', count($followedUserIds)) . 'iii';
        $params = array_merge($followedUserIds, [$offset, $limit, $userId]);

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
            with post_data AS(
                SELECT *
                FROM posts p 
                WHERE status = 'public' AND DATE(p.created_at) = CURDATE()
                ORDER BY COALESCE(scheduled_at, created_at)  DESC, created_at DESC
                LIMIT ?, ?
            ),
            number_of_likes AS(
                SELECT pl.post_id, COUNT(*) AS number_of_likes
                FROM post_likes pl 
                WHERE pl.post_id IN (SELECT post_data.id FROM post_data)
                GROUP BY pl.post_id
            ),
            comment_data AS(
                SELECT c.post_id, COUNT(*) AS number_of_comments
                FROM comments c
                WHERE c.post_id IN (SELECT post_data.id FROM post_data)
                GROUP BY c.post_id
            ),
            user_data AS(
                SELECT u.id, u.account_name, u.username
                FROM users u 
                WHERE u.id IN (SELECT post_data.user_id FROM post_data)
            ),
            profile_data AS(
                SELECT *
                FROM profiles pr
                WHERE pr.user_id IN (SELECT post_data.user_id FROM post_data)
            ),
            user_likes AS (
                SELECT pl.post_id, pl.user_id, 1 AS is_like
                FROM post_likes pl
                WHERE pl.user_id = ? AND pl.post_id IN (SELECT id FROM post_data)
            )
            SELECT pd.*, COALESCE(nol.number_of_likes, 0) AS number_of_likes, COALESCE(cd.number_of_comments,0) AS number_of_comments,
            ud.account_name, ud.username, pr.profile_image_path, pr.extension AS profile_image_extension, COALESCE(ul.is_like,0) AS is_like
            FROM post_data pd
            LEFT JOIN number_of_likes nol ON pd.id = nol.post_id
            LEFT JOIN comment_data cd ON pd.id = cd.post_id
            LEFT JOIN user_data ud ON pd.user_id = ud.id
            LEFT JOIN profile_data pr ON pd.user_id = pr.user_id
            LEFT JOIN user_likes ul ON pd.id = ul.post_id;
            SQL;

        $results = $mysqli->prepareAndFetchAll($query, "iii", [$offset, $limit, $userId]);

        return $results === null ? [] : $this->rawDataToPosts($results);
    }

    public function getTrendPostsForGuest(int $offset, int $limit = 20): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query =
            <<<SQL
            with post_data AS(
                SELECT *
                FROM posts p 
                WHERE status = 'public' AND DATE(p.created_at) = CURDATE()
                ORDER BY COALESCE(scheduled_at, created_at)  DESC, created_at DESC
                LIMIT ?, ?
            ),
            number_of_likes AS(
                SELECT pl.post_id, COUNT(*) AS number_of_likes
                FROM post_likes pl 
                WHERE pl.post_id IN (SELECT post_data.id FROM post_data)
                GROUP BY pl.post_id
            ),
            comment_data AS(
                SELECT c.post_id, COUNT(*) AS number_of_comments
                FROM comments c
                WHERE c.post_id IN (SELECT post_data.id FROM post_data)
                GROUP BY c.post_id
            ),
            user_data AS(
                SELECT u.id, u.account_name, u.username
                FROM users u 
                WHERE u.id IN (SELECT post_data.user_id FROM post_data)
            ),
            profile_data AS(
                SELECT *
                FROM profiles pr
                WHERE pr.user_id IN (SELECT post_data.user_id FROM post_data)
            )
            SELECT pd.*, COALESCE(nol.number_of_likes, 0) AS number_of_likes, COALESCE(cd.number_of_comments,0) AS number_of_comments,
            ud.account_name, ud.username, pr.profile_image_path, pr.extension AS profile_image_extension
            FROM post_data pd
            LEFT JOIN number_of_likes nol ON pd.id = nol.post_id
            LEFT JOIN comment_data cd ON pd.id = cd.post_id
            LEFT JOIN user_data ud ON pd.user_id = ud.id
            LEFT JOIN profile_data pr ON pd.user_id = pr.user_id;
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
            WITH user_data AS (
                SELECT u.id, u.account_name, u.username
                FROM users u 
                WHERE u.username = ?
            ),
            post_data AS(
                    SELECT p.*
                    FROM posts p 
                    WHERE p.user_id IN (SELECT id FROM user_data) AND p.status = 'public'
                    ORDER BY COALESCE(scheduled_at, created_at)  DESC, created_at DESC
                    LIMIT ?, ?
            ),
            comment_counts AS (
                SELECT c.post_id, COUNT(*) AS number_of_comments
                FROM comments c
                WHERE c.post_id IN (SELECT id FROM post_data)
                GROUP BY c.post_id
            ),
            like_counts AS(
                SELECT pl.post_id, COUNT(*) AS number_of_likes
                FROM post_likes pl
                WHERE pl.post_id IN (SELECT id FROM post_data)
                GROUP BY pl.post_id
            ),
            profile_data AS(
                    SELECT pr.user_id, pr.profile_image_path, pr.extension AS profile_image_extension
                    FROM profiles pr
                    WHERE pr.user_id IN (SELECT post_data.user_id FROM post_data)
            ),
            user_likes AS (
                SELECT pl.post_id, pl.user_id, 1 AS is_like
                FROM post_likes pl
                WHERE pl.user_id = ? AND pl.post_id IN (SELECT id FROM post_data)
            )
            SELECT pd.*, ud.account_name, ud.username, COALESCE(cc.number_of_comments, 0) AS number_of_comments, COALESCE(lc.number_of_likes,0) AS number_of_likes,
            prd.profile_image_path, prd.profile_image_extension, COALESCE(ul.is_like,0) AS is_like
            FROM post_data pd
            LEFT JOIN user_data ud ON pd.user_id = ud.id
            LEFT JOIN comment_counts cc ON pd.id = cc.post_id
            LEFT JOIN like_counts lc ON pd.id = lc.post_id
            LEFT JOIN profile_data prd ON pd.user_id = prd.user_id
            LEFT JOIN user_likes ul ON pd.id = ul.post_id;
            SQL;

        $results = $mysqli->prepareAndFetchAll($query, "siii", [$username,$offset, $limit ,$userId]);

        return $results === null ? [] : $this->rawDataToPosts($results);
    }

    // プロトタイプ用の関数
    public function createForProto(int $counter, string $executeAt, Post $post): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $eventName = "random_post_" . $counter;
        $content = mysqli_escape_string($mysqli, $post->getContent());
        $url = mysqli_escape_string($mysqli, $post->getUrl());
        $userId = $post->getUserId();

        $query = <<<SQL
        CREATE EVENT IF NOT EXISTS $eventName
        ON SCHEDULE AT '$executeAt'
        DO
            INSERT INTO posts (status, content, url , user_id) values('public', '$content', '$url', $userId);
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

    public function count(): int
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT COUNT(*) FROM posts";
        $result = $mysqli->prepareAndFetchAll($query, "", [])[0];

        return $result['COUNT(*)'];
    }

    public function getInfluencerPostIds(): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        // プロトタイプではuser_idが1から50がインフルエンサー
        $query = "SELECT id FROM posts WHERE user_id BETWEEN 1 AND 50;";

        $results = $mysqli->prepareAndFetchAll($query, '', []);

        $data = [];
        foreach ($results as $result) {
            $data[] = $result['id'];
        }

        return $data;
    }
}
