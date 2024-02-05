<?php

namespace src\models;

use DateTime;
use src\database\data_access\DAOFactory;
use src\models\interfaces\Model;
use src\models\traits\GenericModel;
use src\models\User;

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
        private ?DateTime $createdAt = null
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

    public function getPostedUser(): User
    {
        $userDao = DAOFactory::getUserDAO();
        $user = $userDao->getById($this->userId);

        return $user;
    }
}
