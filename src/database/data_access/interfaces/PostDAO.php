<?php

namespace src\database\data_access\interfaces;

use src\models\Post;

interface PostDAO
{
    public function create(Post $post): bool;
    public function getById(int $id): ?Post;
    public function getByUserId(int $userId): ?Post;
    public function delete(int $id): bool;
    public function getTwentyPosts(int $userId, int $offset) : array;
}
