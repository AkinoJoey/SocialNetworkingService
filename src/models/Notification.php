<?php 

namespace src\models;

use DateTime;
use src\models\interfaces\Model;
use src\models\traits\GenericModel;

class Notification implements Model{
    use GenericModel;

    public function __construct(
        private int $userId,
        private string $notificationType,
        private int $relatedId,
        private bool $isRead = false,
        private ?int $id = null,
        private ?DateTime $createdAt = null
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getNotificationType() : string {
        return $this->notificationType;
    }

    public function setNotificationType(string $notificationType) : void {
        $this->notificationType = $notificationType;
    }

    public function getRelatedId(): int
    {
        return $this->relatedId;
    }

    public function setRelatedId(int $relatedId): void
    {
        $this->relatedId = $relatedId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getIsRead() : bool {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead) : void {
        $this->isRead = $isRead;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}