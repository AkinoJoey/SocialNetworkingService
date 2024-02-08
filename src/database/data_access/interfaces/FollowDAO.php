<?php

namespace src\database\data_access\interfaces;

use src\models\Follow;

interface FollowDAO
{
    public function create(Follow $follow): bool;
    public function delete(int $followingUserId, int $followerUserId): bool;
}
