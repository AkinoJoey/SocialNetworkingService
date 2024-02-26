<?php

namespace src\database\data_access\interfaces;

use src\models\Comment;

interface CommentDAO
{
    public function create(Comment $comment): bool;
    public function getById(int $id): ?Comment;
    public function delete(int $id, int $userId): bool;
    public function getChildComments(int $parentCommentId, int $userId, int $offset, int $limit = 20): array;
    public function getCommentsToPost(int $postId, int $userId,  int $offset, int $limit = 20): array;
    public function getByUrl(string $url, int $userId): ?Comment;
}
