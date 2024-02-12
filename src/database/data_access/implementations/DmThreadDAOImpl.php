<?php

namespace src\database\data_access\implementations;

use DateTime;
use src\database\data_access\interfaces\DmThreadDAO;
use src\models\DmThread;
use src\database\DatabaseManager;

class DmThreadDAOImpl implements DmThreadDAO
{
    public function create(DmThread $dmThread): bool
    {
        if ($dmThread->getId() !== null) throw new \Exception('Cannot create a dm$dmThread with an existing ID. id: ' . $dmThread->getId());
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO dm_threads(user_id1, user_id2, url) VALUES(?, ?, ?);";

        $result = $mysqli->prepareAndExecute($query, 'iis', [$dmThread->getUserId1(), $dmThread->getUserId2(), $dmThread->getUrl()]);

        if (!$result) return false;

        $dmThread->setId($mysqli->insert_id);

        return true;
    }

    public function getByUserIds(int $userId1, int $userId2): ?DmThread
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM dm_threads WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?);";

        $result = $mysqli->prepareAndFetchAll($query, 'iiii', [$userId1, $userId2, $userId2, $userId1])[0] ?? null;

        if ($result === null) return null;

        return $this->rawDataToDmThread($result);
    }

    public function getByUserIdAndUrl(int $userId, string $url): ?DmThread
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM dm_threads WHERE url = ? AND(user_id1 = ? OR user_id2 = ?)";

        $result = $mysqli->prepareAndFetchAll($query, 'sii', [$url, $userId, $userId])[0] ?? null;

        if($result === null) return null;

        return $this->rawDataToDmThread($result);
        
    }

    private function rawDataToDmThread(array $rowData): DmThread
    {
        return new DmThread(
            url: $rowData['url'],
            userId1: $rowData['user_id1'],
            userId2: $rowData['user_id2'],
            id: $rowData['id'],
            createdAt: new DateTime($rowData['created_at'])
        );
    }
}
