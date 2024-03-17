<?php 

namespace src\models;

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
        private ?bool $isRead = false,
        private ?int $id = null,
        private ?DataTimeStamp $timeStamp = null,
        private ?string $accountName = null,
        private ?string $username = null,
        private ?string $commentUrl = null,
        private ?string $postUrl = null,
        private ?string $threadUrl = null,
        private ?string $profileImagePath = null,
        private ?string $profileImageExtension = null
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

    public function getTimeStamp(): ?DataTimeStamp
    {
        return $this->timeStamp;
    }

    public function setTimeStamp(DataTimeStamp $timeStamp): void
    {
        $this->timeStamp = $timeStamp;
    }

    public function getAccountName() : ?string {
        return $this->accountName;
    }

    public function getUsername() : ?string {
        return $this->username;
    }

    public function getCommentUrl() : ?string {
        return $this->commentUrl;
    }

    public function getPostUrl() : ?string {
        return $this->postUrl;
    }

    public function getThreadUrl() : ?string {
        return $this->threadUrl;
    }

    public function getProfileImagePath(): ?string
    {
        return $this->profileImagePath;
    }

    public function getProfileImageExtension(): ?string
    {
        return $this->profileImageExtension;
    }
}