<?php

namespace src\models;

use DateTime;
use src\models\interfaces\Model;
use src\models\traits\GenericModel;

class DmThread implements Model
{
    use GenericModel;

    public function __construct(
        private string $url,
        private int $userId1,
        private int $userId2,
        private ?int $id = null,
        private ?DateTime $createdAt = null,
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId1() : int {
        return $this->userId1;
    }

    public function setUserId1(int $userId) : void {
        $this->userId1 = $userId;
    }

    public function getUserId2(): int
    {
        return $this->userId2;
    }

    public function setUserId2(int $userId): void
    {
        $this->userId2 = $userId;
    }

    public function getUrl() : string {
        return $this->url;
    }

    public function setUrl(string $url) : void {
        $this->url = $url;
    }

    public function getCreatedAt() : ?DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt) : void {
        $this->createdAt = $createdAt;
    }
}
