<?php 

namespace src\models;

use DateTime;
use src\models\interfaces\Model;
use src\models\traits\GenericModel;

class PostLikeNotification implements Model{
    use GenericModel;

    public function __construct(
        private int $userId,
        private int $postId,
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

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
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