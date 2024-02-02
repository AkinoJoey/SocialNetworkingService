<?php

namespace src\models;

use DateTime;
use src\models\interfaces\Model;
use src\models\traits\GenericModel;

class Post implements Model
{
    use GenericModel;

    public function __construct(
        private string $content,
        private int $userId,
        private ?int $id = null,
        private ?string $mediaPath = null,
        private ?DateTime $scheduledAt = null,
        private ?DataTimeStamp $timeStamp = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(String $content): void
    {
        $this->content = $content;
    }

    public function getMediaPath(): ?string
    {
        return $this->mediaPath;
    }

    public function setMediaPath(String $mediaPath): void
    {
        $this->mediaPath = $mediaPath;
    }

    public function getScheduledAt(): ?DateTime
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(DateTime $scheduledAt): void
    {
        $this->scheduledAt = $scheduledAt;
    }
    
    public function getTimeStamp(): ?DataTimeStamp
    {
        return $this->timeStamp;
    }

    public function setTimeStamp(DataTimeStamp $timeStamp): void
    {
        $this->timeStamp = $timeStamp;
    }
}