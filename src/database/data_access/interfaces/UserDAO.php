<?php

namespace src\database\data_access\interfaces;

use src\models\User;

interface UserDAO
{
    public function create(User $user, string $password): bool;
    public function getById(int $id): ?User;
    public function getByEmail(string $email): ?User;
    public function getByUsername(string $username): ?User;
    public function getHashedPasswordById(int $id): ?string;
    public function update(User $user, string $password = null): bool;
    public function getUserListForSearch(string $keyword, int $limit = 100) : array;
    public function getTopFollowedUsers(int $limit = 100) : array;
    public function delete(int $id) : bool;
    public function deleteExpiredEmailVerificationUsers() : bool;
}
