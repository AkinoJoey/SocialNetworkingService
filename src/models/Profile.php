<?php

namespace src\models;

use src\models\interfaces\Model;
use src\models\traits\GenericModel;

class Profile implements Model
{
    use GenericModel;

    public function __construct(
        private int $userId,
        private ?int $id = null,
        private ?int $age = null,
        private ?string $location = null,
        private ?string $description = null,
        private ?string $profileImagePath = null,
        private ?string $extension = null,
        private ?DataTimeStamp $timeStamp = null,
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getProfileImagePath(): ?string
    {
        return $this->profileImagePath;
    }

    public function setProfileImagePath(string  $profileImagePath): void
    {
        $this->profileImagePath = $profileImagePath;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
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
