<?php 

namespace src\database\data_access\interfaces;

use src\models\PostLikeNotification;

interface PostLikeNotificationDAO{
    public function create(PostLikeNotification $postLikeNotification): bool;
    public function delete(int $userId, int $postId) : bool;
    public function update(PostLikeNotification $postLikeNotification) : bool;
    public function getById(int $id) : ?PostLikeNotification;
}