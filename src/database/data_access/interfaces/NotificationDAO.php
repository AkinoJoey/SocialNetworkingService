<?php

namespace src\database\data_access\interfaces;

use src\models\Notification;

interface NotificationDAO
{
    public function create(Notification $notification): bool;
    public function delete(int $userId, string $notificationType, int $sourceId, ?int $postId, ?int $commentId, ?int $dmThreadId): bool;
    public function update(Notification $notification): bool;
    public function updateReadStatus(int $id, bool $status = true): bool;
    public function getById(int $id): ?Notification;
    public function getNotificationList(int $userId, int $limit = 100): array;
    public function getNumberOfNotification(int $userId) : int;
}
