<?php

namespace src\database\data_access\interfaces;

use src\models\CommentLike;

interface CommentLikeDAO
{
    public function create(CommentLike $commentLike): bool;
    public function getByUserIdAndPostId(int $userId, int $commentId): ?CommentLike;
    public function delete(int $userId, int $commentId): bool;
    public function getNumberOfLikes(int $commentId): int;
}
