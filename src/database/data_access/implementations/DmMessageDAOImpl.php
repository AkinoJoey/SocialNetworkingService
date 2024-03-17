<?php

namespace src\database\data_access\implementations;

use DateTime;
use Exception;
use src\database\data_access\interfaces\DmMessageDAO;
use src\models\DmMessage;
use src\database\DatabaseManager;
use src\helpers\CipherHelper;

class DmMessageDAOImpl implements DmMessageDAO
{
    public function create(DmMessage $dmMessage): bool
    {
        if ($dmMessage->getId() !== null) throw new Exception('Cannot create a message with an existing ID. id: ' . $dmMessage->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO dm_messages (message, iv, sender_user_id, receiver_user_id, dm_thread_id) VALUES (?, ?, ?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'ssiii',
            [
                $dmMessage->getMessage(),
                $dmMessage->getIv(),
                $dmMessage->getSenderUserId(),
                $dmMessage->getReceiverUserId(),
                $dmMessage->getDmThreadId()
            ]
        );

        if (!$result) return false;

        $dmMessage->setId($mysqli->insert_id);

        return true;
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM dm_messages WHERE id = ?", 'i', [$id]);
    }

    public function getById(int $id): DmMessage
    {
        $postRaw = $this->getRawById($id);
        if ($postRaw === null) return null;

        return $this->rawDataToDmMessage($postRaw);
    }

    private function getRawById(int $id): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM dm_messages WHERE id = ?";

        $result = $mysqli->prepareAndFetchAll($query, 'i', [$id])[0] ?? null;

        if ($result === null) return null;

        return $result;
    }

    public function getOneHundredByDmThreadId(int $dmThreadId): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM dm_messages WHERE dm_thread_id = ? ORDER BY created_at LIMIT 100";

        $results = $mysqli->prepareAndFetchAll($query, 'i', [$dmThreadId]);

        return $results === null ? [] : $this->rawDataToDmMessages($results);
    }

    private function rawDataToDmMessage(array $rowData): DmMessage
    {
        $decryptedMessage = CipherHelper::decryptMessage($rowData['message'], $rowData['iv']);

        return new DmMessage(
            message: $decryptedMessage,
            senderUserId: $rowData['sender_user_id'],
            receiverUserId: $rowData['receiver_user_id'],
            dmThreadId: $rowData['dm_thread_id'],
            id: $rowData['id'],
            createdAt: new DateTime($rowData['created_at'])
        );
    }

    private function rawDataToDmMessages(array $results): array
    {
        $messages = [];

        foreach ($results as $result) {
            $messages[] = $this->rawDataToDmMessage($result);
        }

        return $messages;
    }

    public function getMessageList(int $userId, int $limit = 100): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query =
            <<<SQL
            WITH latest_messages AS (
                SELECT dm_thread_id, 
                    MAX(created_at) AS latest_created_at
                FROM dm_messages
                WHERE sender_user_id = ? OR receiver_user_id = ?
                GROUP BY dm_thread_id
            )
            SELECT dm.id, dm.message, dm.iv, dm.dm_thread_id, dm.created_at , u.account_name as from_user_account_name,p.profile_image_path, p.extension, dm.sender_user_id , dm.receiver_user_id , dt.url
            FROM dm_messages dm
            JOIN latest_messages latest ON dm.dm_thread_id = latest.dm_thread_id AND dm.created_at = latest.latest_created_at
            LEFT JOIN users u ON CASE WHEN dm.sender_user_id = ? THEN dm.receiver_user_id ELSE dm.sender_user_id END = u.id
            LEFT JOIN profiles p ON u.id = p.user_id
            LEFT JOIN dm_threads dt on dm.dm_thread_id = dt.id
            LIMIT ?;
            SQL;

        $results = $mysqli->prepareAndFetchAll($query, 'iiii', [$userId, $userId, $userId, $limit]);

        return $results === null ? [] : $this->rawDataToDmMessagesForList($results);
    }

    private function rawDataToDmMessagesForList(array $results): array
    {
        $messages = [];

        foreach ($results as $result) {
            $messages[] = $this->rawDataToDmMessageForList($result);
        }

        return $messages;
    }

    private function rawDataToDmMessageForList(array $rowData): DmMessage
    {
        $decryptedMessage = CipherHelper::decryptMessage($rowData['message'], $rowData['iv']);

        return new DmMessage(
            message: $decryptedMessage,
            senderUserId: $rowData['sender_user_id'],
            receiverUserId: $rowData['receiver_user_id'],
            dmThreadId: $rowData['dm_thread_id'],
            id: $rowData['id'],
            createdAt: new DateTime($rowData['created_at']),
            from_user_account_name: $rowData['from_user_account_name'],
            url: $rowData['url'],
            profileImagePath: $rowData['profile_image_path'],
            profileImageExtension: $rowData['extension']
        );
    }
}
