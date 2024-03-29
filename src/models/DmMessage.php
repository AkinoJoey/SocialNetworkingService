<?php 

namespace src\models;

use DateTime;
use src\models\interfaces\Model;
use src\models\traits\GenericModel;

class DmMessage implements Model{

    use GenericModel;

    public function __construct(
        private string $message,
        private int $senderUserId,
        private int $receiverUserId,
        private int $dmThreadId,
        private ?int $id = null,
        private ?string $iv = null,
        private ?DateTime $createdAt = null,
        private ?string $from_user_account_name = null,
        private ?string $url = null,
        private ?string $profileImagePath = null,
        private ?string $profileImageExtension = null
    ) {
    }

    public function getId() : ?int {
        return $this->id;
    }

    public function setId(int $id) : void {
        $this->id = $id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getIv(): ?string
    {
        return $this->iv;
    }

    public function setIv(string $iv): void
    {
        $this->iv = $iv;
    }

    public function getSenderUserId() : int {
        return $this->senderUserId;
    }

    public function setSenderUserId(int $userId) : void {
        $this->senderUserId = $userId;
    }

    public function getReceiverUserId(): int
    {
        return $this->receiverUserId;
    }

    public function setReceiverUserId(int $userId): void
    {
        $this->receiverUserId = $userId;
    }

    public function getDmThreadId(): int
    {
        return $this->dmThreadId;
    }

    public function setDmThreadId(int $dmThreadId): void
    {
        $this->dmThreadId = $dmThreadId;
    }

    public function getCreatedAt() : ?DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt) : void {
        $this->createdAt = $createdAt;
    }

    public function getFromUserAccountName() : ?string {
        return $this->from_user_account_name;
    }

    public function getUrl() : ?string {
        return $this->url;
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