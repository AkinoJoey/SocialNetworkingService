<?php

namespace src\models;

class DataTimeStamp
{
    private string $createdAt;
    private string $updatedAt;

    public function __construct(string $createdAt, string $updatedAt)
    {
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
