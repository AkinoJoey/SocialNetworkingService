<?php

namespace src\database\data_access\interfaces;

use src\models\Follow;

interface FollowDAO
{
    public function create(Follow $follow): bool;
    public function delete(int $followingUserId, int $followerUserId): bool;
    public function getFollowingUserIdList(int $userId) : array;
    public function getFollowerUserIdList(int $userId) : array;
    public function isFollow(int $following_user_id, int $follower_user_id): bool;
}
