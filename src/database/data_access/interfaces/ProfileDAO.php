<?php

namespace src\database\data_access\interfaces;

use src\models\Profile;

interface ProfileDAO
{
    public function create(Profile $profile): bool;
    public function getById(int $id): ?Profile;
    public function getByUserId(string $userId): ?Profile;
    public function update(Profile $profile): bool;
}
