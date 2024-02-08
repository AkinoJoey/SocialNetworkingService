<?php

namespace src\models;

use DateTime;
use src\models\interfaces\Model;
use src\models\traits\GenericModel;

class Follow implements Model
{
    use GenericModel;

    public function __construct(
        private int $followingUserId,
        private int $followerUserId,
        private ?DateTime $createdAt = null
    ) {
    }

    public function getFollowingUserId() : int {
        return $this->followingUserId;
    }

    public function setFollowingUserId(int $userId) : void {
        $this->followingUserId = $userId;
    }

    public function getFollowerUserId(): int
    {
        return $this->followerUserId;
    }

    public function setFollowerUserId(int $userId): void
    {
        $this->followerUserId = $userId;
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
