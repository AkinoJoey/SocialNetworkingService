<?php

namespace src\database\data_access\interfaces;

use src\models\User;

interface UserDAO
{
    public function create(User $user, string $password): bool;
    public function getById(int $id): ?User;
    public function getByEmail(string $email): ?User;
    public function getHashedPasswordById(int $id): ?string;
    public function update(User $user): bool;
}
