<?php

namespace src\database\data_access\implementations;

use DateTime;
use Exception;
use src\database\data_access\interfaces\DmMessageDAO;
use src\models\DmMessage;
use src\database\DatabaseManager;

class DmMessageDAOImpl implements DmMessageDAO
{
    public function create(DmMessage $dmMessage): bool
    {
        if ($dmMessage->getId() !== null) throw new Exception('Cannot create a message with an existing ID. id: ' . $dmMessage->getId());

        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO dm_messages (text, sender_user_id, receiver_user_id, dm_thread_id) VALUES (?, ?, ?, ?)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'siii',
            [
                $dmMessage->getText(),
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
        return new DmMessage(
            text: $rowData['text'],
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
}
