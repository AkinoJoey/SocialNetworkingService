<?php

namespace src\database\data_access\interfaces;

use src\models\PasswordResetToken;

interface PasswordResetTokenDAO
{
    public function create(PasswordResetToken $passwordResetToken): bool;
    public function getByToken(string $token) : ?PasswordResetToken;
    public function getByUserId(int $userId) : ?PasswordResetToken;
    public function deleteByUserId(int $id) : bool;
}
