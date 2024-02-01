<?php

namespace src\models;

use src\models\interfaces\Model;
use src\models\traits\GenericModel;

class User implements Model
{
    use GenericModel;

    public function __construct(
        private string $accountName,
        private string $email,
        private string $username,
        private ?int $id = null,
        private bool $emailVerified = false,
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

    public function getAccountName(): ?string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): void
    {
        $this->accountName = $accountName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getTimeStamp(): ?DataTimeStamp
    {
        return $this->timeStamp;
    }

    public function setTimeStamp(DataTimeStamp $timeStamp): void
    {
        $this->timeStamp = $timeStamp;
    }

    public function setEmailVerified(bool $emailVerified): void
    {
        $this->emailVerified = $emailVerified;
    }

    public function getEmailVerified(): bool
    {
        return $this->emailVerified;
    }
}
