<?php

namespace src\database\data_access\interfaces;

use src\models\PostLike;

interface PostLikeDAO
{
    public function create(PostLike $postLike): bool;
    public function getByUserIdAndPostId(int $userId, int $postId): ?PostLike;
    public function delete(int $userId, int $postId): bool;
    public function getNumberOfLikes(int $postId) : int;
}
