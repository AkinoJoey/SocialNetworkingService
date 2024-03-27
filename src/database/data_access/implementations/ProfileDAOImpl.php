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

        $query = "INSERT INTO profiles (age ,location, description, profile_image_path, extension,  user_id) VALUES (?, ?, ?, ?,  ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'issssi',
            [
                $profile->getAge(),
                $profile->getLocation(),
                preg_replace("/(\R{3,})/", "\n\n", $profile->getDescription()),//3行以上の改行は2行にする
                $profile->getProfileImagePath(),
                $profile->getExtension(),
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

    public function getByUserId(int $userId): ?Profile
    {
        $profileRow = $this->getRowByUserId($userId);
        if($profileRow === null) return null;

        return $this->rawDataToProfile($profileRow);
    }

    private function getRowByUserId(int $userId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM profiles WHERE user_id = ?";

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
            extension: $rawData['extension'],
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
            UPDATE profiles
                SET 
                    age = ?,
                    location = ?,
                    description = ?,
                    profile_image_path = CASE WHEN ? IS NULL THEN profile_image_path ELSE ? END,
                    extension = CASE WHEN ? IS NULL THEN extension ELSE ? END
                WHERE id = ?
            SQL;

        $result = $mysqli->prepareAndExecute(
            $query,
            'issssssi',
            [
                $profile->getAge(),
                $profile->getLocation(),
                $profile->getDescription(),
                $profile->getProfileImagePath(),
                $profile->getProfileImagePath(),
                $profile->getExtension(),
                $profile->getExtension(),
                $profile->getId()
            ]
        );

        if (!$result) return false;

        return true;
    }
}
