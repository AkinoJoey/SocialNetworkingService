<?php

namespace src\models;

use DateTime;
use src\models\interfaces\Model;
use src\models\traits\GenericModel;

class Comment implements Model
{
    use GenericModel;

    public function __construct(
        private string $content,
        private string $url,
        private int $userId,
        private ?int $id = null,
        private ?int $postId = null,
        private ?int $parentCommentId = null,
        private ?string $mediaPath = null,
        private ?DateTime $createdAt = null,
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

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    public function getParentCommentId(): ?int
    {
        return $this->parentCommentId;
    }

    public function setParentCommentId(int $parentCommentId): void
    {
        $this->parentCommentId = $parentCommentId;
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


    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
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
