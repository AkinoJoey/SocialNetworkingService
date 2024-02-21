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
        private string $url,
        private int $userId,
        private ?int $id = null,
        private ?string $mediaPath = null,
        private ?DateTime $scheduledAt = null,
        private ?DataTimeStamp $timeStamp = null,
        private ?string $username = null,
        private ?string $accountName = null,
        private ?int $numberOfComments = null,
        private ?int $numberOfLikes = null,
        private ?int $isLike = null
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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(String $url): void
    {
        $this->url = $url;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getAccountName(): ?string
    {
        return $this->accountName;
    }

    public function getNumberOfComments(): ?int
    {
        return $this->numberOfComments;
    }

    public function getNumberOfLikes(): ?int
    {
        return $this->numberOfLikes;
    }

    public function getIsLike(): ?int
    {
        return $this->isLike;
    }
}
