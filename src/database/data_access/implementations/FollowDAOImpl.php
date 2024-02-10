<?php

namespace src\database\data_access\implementations;

use src\database\data_access\interfaces\FollowDAO;
use src\models\Follow;
use src\database\DatabaseManager;

class FollowDAOImpl implements FollowDAO
{

    public function create(Follow $follow): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO follows (following_user_id, follower_user_id) VALUES (?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'ii',
            [
                $follow->getFollowingUserId(),
                $follow->getFollowerUserId(),
            ]
        );

        if (!$result) return false;

        return true;
    }

    public function delete(int $followingUserId, int $followerUserId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM follows WHERE following_user_id = ? AND follower_user_id = ?", 'ii', [$followingUserId, $followerUserId]);
    }

    public function getFollowingUserIdList(int $userId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT follower_user_id FROM follows where following_user_id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$userId]) ?? null;


        return $result !== null ? array_column($result, 'follower_user_id') : null;
    }

    public function getFollowerUserIdList(int $userId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT following_user_id FROM follows where follower_user_id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$userId]) ?? null;

        return $result !== null ? array_column($result, 'following_user_id') : null;
    }

    public function isFollow(int $following_user_id, int $follower_user_id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT follower_user_id FROM follows where following_user_id = ? AND follower_user_id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'ii', [$following_user_id, $follower_user_id])[0] ?? null;

        return $result !== null;
    }
}
