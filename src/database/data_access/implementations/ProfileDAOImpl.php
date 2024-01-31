<?php

namespace src\database\data_access\implementations;

use src\database\data_access\interfaces\ProfileDAO;
use src\database\DatabaseManager;
use src\models\DataTimeStamp;
use src\models\Profile;

class ProfileDAOImpl implements ProfileDAO
{
    public function create(Profile $profile): bool
    {
        if ($profile->getId() !== null) throw new \Exception('Cannot create a profile with an existing ID. id: ' . $profile->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO profiles (age ,location, description, profile_image_path, user_id) VALUES (?, ?, ?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'isssi',
            [
                $profile->getAge(),
                $profile->getLocation(),
                $profile->getDescription(),
                $profile->getProfileImagePath(),
                $profile->getUserId()
            ]
        );

        if (!$result) return false;

        $profile->setId($mysqli->insert_id);

        return true;
    }
    private function getRawById(int $id): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM profiles WHERE id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$id])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    public function getById(int $id) : ?Profile {
        $profileRaw = $this->getRawById($id);
        if ($profileRaw === null) return null;

        return $this->rawDataToProfile($profileRaw);
    }

    public function getByUserId(string $userId): ?Profile
    {
        $profileRow = $this->getRowByUserId($userId);
        if($profileRow === null) return null;

        return $this->rawDataToProfile($profileRow);
    }

    private function getRowByUserId(string $userId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM users WHERE user_id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$userId])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    private function rawDataToProfile(array $rawData): Profile
    {
        return new Profile(
            userId: $rawData['user_id'],
            id: $rawData['id'],
            age: $rawData['age'],
            location: $rawData['location'],
            description: $rawData['description'],
            profileImagePath: $rawData['profile_image_path'],
            timeStamp: new DataTimeStamp($rawData['created_at'], $rawData['updated_at'])
        );
    }

    public function update(Profile $profile) : bool {
        if ($profile->getId() === null) throw new \Exception('Profile specified has no ID.');

        $current = $this->getById($profile->getId());
        if ($current === null) throw new \Exception(sprintf("Profile %s does not exist.", $profile->getId()));

        $mysqli = DatabaseManager::getMysqliConnection();

        $query =
            <<<SQL
            UPDATE profile
                SET 
                    age = ?,
                    locations = ?,
                    description = ?,
                    profile_image_path = ?
                WHERE id = ?
            SQL;

        $result = $mysqli->prepareAndExecute(
            $query,
            'isssi',
            [
                $profile->getAge(),
                $profile->getLocation(),
                $profile->getDescription(),
                $profile->getDescription(),
                $profile->getProfileImagePath(),
                $profile->getId()
            ]
        );

        if (!$result) return false;

        return true;
    }
}