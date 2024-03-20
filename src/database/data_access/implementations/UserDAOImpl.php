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

        $query = "INSERT INTO users (account_name ,email, password, email_verified, username) VALUES (?, ?, ?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'sssis',
            [
                $user->getAccountName(),
                $user->getEmail(),
                password_hash($password, PASSWORD_DEFAULT), // store the hashed password
                $user->getEmailVerified(),
                $user->getUsername()
            ]
        );

        if (!$result) return false;

        $user->setId($mysqli->insert_id);

        return true;
    }

    private function getRawById(int $id): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT u.*, p.profile_image_path , p.extension FROM users u LEFT JOIN profiles p ON u.id = p.user_id WHERE u.id = ?";

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

    private function getRawByUsername(string $username): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM users WHERE username = ?";

        $result = $mysqli->prepareAndFetchAll($query, 's', [$username])[0] ?? null;

        if ($result === null) return null;
        return $result;
    }

    private function rawDataToUser(array $rawData): User
    {
        return new User(
            accountName: $rawData['account_name'],
            email: $rawData['email'],
            id: $rawData['id'],
            username: $rawData['username'],
            emailVerified: $rawData['email_verified'],
            timeStamp: new DataTimeStamp($rawData['created_at'], $rawData['updated_at']),
            profileImagePath: $rawData['profile_image_path'] ?? null,
            profileImageExtension: $rawData['extension'] ?? null
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

    public function getByUsername(string $username): ?User
    {
        $userRaw = $this->getRawByUsername($username);
        if ($userRaw === null) return null;

        return $this->rawDataToUser($userRaw);
    }

    public function getHashedPasswordById(int $id): ?string
    {
        return $this->getRawById($id)['password'] ?? null;
    }

    public function update(User $user, string $password = null): bool
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
                    username = ?,
                    email_verified = ?
                WHERE id = ?
            SQL;

        $result = $mysqli->prepareAndExecute(
            $query,
            'ssssii',
            [
                $user->getAccountName(),
                $user->getEmail(),
                $password === null ? $this->getHashedPasswordById($user->getId()) : password_hash($password, PASSWORD_DEFAULT),
                $user->getUsername(),
                $user->getEmailVerified(),
                $user->getId()
            ]
        );

        if (!$result) return false;

        return true;
    }

    public function getUserListForSearch(string $keyword, int $limit = 100): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM users u WHERE u.account_name LIKE ? or u.username LIKE ? LIMIT ?;";

        $param = "%" . $keyword . "%";
        $results = $mysqli->prepareAndFetchAll($query, 'ssi', [$param, $param, $limit]) ?? null;

        return $results === null ? [] : $this->rawDataToUsers($results);
    }

    public function getTopFollowedUsers(int $limit = 100): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query =
            <<<SQL
            WITH number_of_followers AS(	
            SELECT f.follower_user_id , count(*) AS number_of_followers
                FROM follows f 
                GROUP BY f.follower_user_id
            )
            SELECT u.*, pr.profile_image_path, pr.extension
                FROM users u 
                LEFT JOIN number_of_followers nof ON u.id = nof.follower_user_id
                LEFT JOIN profiles pr ON u.id = pr.user_id
                ORDER BY nof.number_of_followers DESC LIMIT ?;
            SQL;

        $results = $mysqli->prepareAndFetchAll($query, 'i', [$limit]) ?? null;

        return $results === null ? [] : $this->rawDataToUsers($results);
    }

    private function rawDataToUsers(array $results): array
    {
        $users = [];

        foreach ($results as $result) {
            $users[] = $this->rawDataToUser($result);
        }

        return $users;
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "DELETE FROM users WHERE id = ?";

        $result = $mysqli->prepareAndExecute($query, 'i', [$id]);
        return $result;
    }
}
