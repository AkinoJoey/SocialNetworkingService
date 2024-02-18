<?php 

namespace src\models;

use DateTime;
use src\models\interfaces\Model;
use src\models\traits\GenericModel;

class Notification implements Model{
    use GenericModel;

    public function __construct(
        private int $userId,
        private int $sourceId,
        private string $notificationType,
        private ?int $postId = null,
        private ?int $commentId = null,
        private ?int $dmThreadId = null,
        private ?bool $isRead = null,
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

    public function getSourceId(): int
    {
        return $this->sourceId;
    }

    public function setSourceId(int $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function getNotificationType() : string {
        return $this->notificationType;
    }

    public function setNotificationType(string $notificationType) : void {
        $this->notificationType = $notificationType;
    }

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    public function getCommentId(): ?int
    {
        return $this->commentId;
    }

    public function setCommentId(int $commentId): void
    {
        $this->commentId = $commentId;
    }

    public function getDmThreadId(): ?int
    {
        return $this->dmThreadId;
    }

    public function setDmThreadId(int $dmThreadId): void
    {
        $this->dmThreadId = $dmThreadId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getIsRead() : ?bool {
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