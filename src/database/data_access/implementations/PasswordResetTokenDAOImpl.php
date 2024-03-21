<?php

namespace src\database\data_access\implementations;

use DateTime;
use src\database\data_access\interfaces\PasswordResetTokenDAO;
use src\models\PasswordResetToken;
use src\database\DatabaseManager;

class PasswordResetTokenDAOImpl implements PasswordResetTokenDAO
{
    public function create(PasswordResetToken $passwordResetToken): bool
    {
        if ($passwordResetToken->getId() !== null) throw new \Exception('Cannot create a password reset token with an existing ID. id: ' . $passwordResetToken->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO password_reset_tokens (user_id, token) VALUES (?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'is',
            [
                $passwordResetToken->getUserId(),
                $passwordResetToken->getToken()
            ]
        );

        if (!$result) return false;

        $passwordResetToken->setId($mysqli->insert_id);

        return true;
    }


    public function getByToken(string $token): ?PasswordResetToken
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM password_reset_tokens WHERE token = ?";

        $result = $mysqli->prepareAndFetchAll($query, 's', [$token])[0] ?? null;

        if ($result === null) return null;

        return $this->rawDataToPasswordResetToken($result);
    }

    public function getByUserId(int $userId): ?PasswordResetToken
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM password_reset_tokens WHERE user_id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$userId])[0] ?? null;

        if ($result === null) return null;

        return $this->rawDataToPasswordResetToken($result);
    }

    private function rawDataToPasswordResetToken(array $rawData): PasswordResetToken
    {
        return new PasswordResetToken(
            userId: $rawData['user_id'],
            token: unpack('H*', $rawData['token'])[1],
            id: $rawData['id'],
            createdAt: new DateTime($rawData['created_at'])
        );
    }

    public function deleteByUserId(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "DELETE FROM password_reset_tokens WHERE user_id = ?";
        $result = $mysqli->prepareAndExecute($query, 'i', [$id]);
        return $result;
    }
}
