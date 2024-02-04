<?php

namespace src\database\data_access\implementations;

use src\database\data_access\interfaces\PostDAO;
use src\database\DatabaseManager;
use src\models\DataTimeStamp;
use src\models\Post;

class PostDAOImpl implements PostDAO
{
    public function create(Post $post): bool
    {
        if ($post->getId() !== null) throw new \Exception('Cannot create a post with an existing ID. id: ' . $post->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO posts (content, url ,media_path, user_id, scheduled_at) VALUES (?, ? ,?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'sssis',
            [
                $post->getContent(),
                $post->getUrl(),
                $post->getMediaPath(),
                $post->getUserId(),
                $post->getScheduledAt(),
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

    private function getRawByUrl(string $url): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM posts WHERE url = ?";

        $result = $mysqli->prepareAndFetchAll($query, 's', [$url])[0] ?? null;

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

    public function getByUrl(string $url): ?Post
    {
        $postRow = $this->getRawByUrl($url);
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

    private function rawDataToPost(array $rawData): Post
    {   
        return new Post(
            content: $rawData['content'],
            url: $rawData['url'],
            userId: $rawData['user_id'],
            id: $rawData['id'],
            mediaPath: $rawData['media_path'],
            scheduledAt: $rawData['scheduled_at'],
            timeStamp: new DataTimeStamp($rawData['created_at'], $rawData['updated_at'])
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

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM posts WHERE id = ?", 'i', [$id]);
    }

    public function getTwentyPosts(int $userId, int $offset, int $limit = 20): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?";

        $results = $mysqli->prepareAndFetchAll($query, 'iii', [$userId, $offset, $limit]);

        return $results === null ? [] : $this->rawDataToPosts($results);
    }

}
