<?php

namespace src\database\data_access\interfaces;

use src\models\Comment;

interface CommentDAO
{
    public function create(Comment $comment): bool;
    public function getById(int $id): ?Comment;
    public function delete(int $id): bool;
    public function getChildComments(int $parentId, int $offset, int $limit = 20): array;
    public function getCommentsToPost(int $postId, int $offset, int $limit = 20): array;
    public function getByUrl(string $url): ?Comment;
}
