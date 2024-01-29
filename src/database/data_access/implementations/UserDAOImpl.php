<?php

namespace src\database\data_access\implementations;

use src\database\data_access\interfaces\UserDAO;
use src\database\DatabaseManager;
use src\models\DataTimeStamp;
use src\models\User;

class UserDAOImpl implements UserDAO
{
    public function create(User $user, string $password): bool
    {
        if ($user->getId() !== null) throw new \Exception('Cannot create a user with an existing ID. id: ' . $user->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO users (account_name ,email, password, email_verified) VALUES (?, ?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'sssi',
            [
                $user->getAccountName(),
                $user->getEmail(),
                password_hash($password, PASSWORD_DEFAULT), // store the hashed password
                $user->getEmailVerified()
            ]
        );

        if (!$result) return false;

        $user->setId($mysqli->insert_id);

        return true;
    }

    private function getRawById(int $id): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM users WHERE id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$id])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    private function getRawByEmail(string $email): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM users WHERE email = ?";

        $result = $mysqli->prepareAndFetchAll($query, 's', [$email])[0] ?? null;

        if ($result === null) return null;
        return $result;
    }

    private function rawDataToUser(array $rawData): User
    {
        return new User(
            accountName: $rawData['account_name'],
            email: $rawData['email'],
            id: $rawData['id'],
            emailVerified: $rawData['email_verified'],
            timeStamp: new DataTimeStamp($rawData['created_at'], $rawData['updated_at'])
        );
    }

    public function getById(int $id): ?User
    {
        $userRaw = $this->getRawById($id);
        if ($userRaw === null) return null;

        return $this->rawDataToUser($userRaw);
    }

    public function getByEmail(string $email): ?User
    {
        $userRaw = $this->getRawByEmail($email);
        if ($userRaw === null) return null;

        return $this->rawDataToUser($userRaw);
    }

    public function getHashedPasswordById(int $id): ?string
    {
        return $this->getRawById($id)['password'] ?? null;
    }

    public function update(User $user): bool
    {
        if ($user->getId() === null) throw new \Exception('User specified has no ID.');

        $current = $this->getById($user->getId());
        if ($current === null) throw new \Exception(sprintf("User %s does not exist.", $user->getId()));

        $mysqli = DatabaseManager::getMysqliConnection();

        $query =
            <<<SQL
            UPDATE users
                SET 
                    account_name = ?,
                    email = ?,
                    password = ?,
                    email_verified = ?
                WHERE id = ?
            SQL;

        $result = $mysqli->prepareAndExecute(
            $query,
            'sssii',
            [
                $user->getAccountName(),
                $user->getEmail(),
                $this->getHashedPasswordById($user->getId()),
                $user->getEmailVerified(),
                $user->getId()
            ]
        );

        if (!$result) return false;

        return true;
    }
}
