<?php

namespace src\helpers;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use src\types\ValueType;
use src\helpers\ValidationHelper;
use src\database\data_access\DAOFactory;
use src\models\DmMessage;
use Exception;
use src\helpers\ChatClient;

class Chat implements MessageComponentInterface
{
    protected $threads = []; // [threadId => [resourceId => ChatClient, resourceId=>ChatClient]]
    protected $clients = []; // [resourceId => ChatClient ]


    public function __construct()
    {
    }

    public function onOpen(ConnectionInterface $conn)
    {

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        // TODO: typeのサニタイズ
        switch ($data['type']) {
            case 'join':
                $this->join($from, $data);
                break;

            case 'message':
                $this->sendMessage($data);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // threadからclientを削除
        $client = $this->clients[$conn->resourceId];
        $threadId = $client->getJoinedThreadId();
        $thread = $this->threads[$threadId];

        unset($thread[$client->getConn()->resourceId]);

        // threadが空の場合はthreadを削除
        if (count($thread) < 1) unset($this->threads[$threadId]);

        // clientsから削除
        unset($this->clients[$conn->resourceId]);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    private function join(ConnectionInterface $from, array $data): void
    {
        // TODO: error handling
        $required_fields = [
            'dm_thread_id' => ValueType::INT,
            'sender_user_id' => ValueType::INT,
            'receiver_user_id' => ValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $data, true);

        $client = new ChatClient($from, $validatedData['sender_user_id'], $validatedData['dm_thread_id']);

        $this->clients[$from->resourceId] = $client;
        $this->threads[$validatedData['dm_thread_id']][$from->resourceId] = $client;

        echo "Starting chat! ({$from->resourceId})\n";
    }

    private function sendMessage(array $data): void
    {
        $required_fields = [
            'dm_thread_id' => ValueType::INT,
            'sender_user_id' => ValueType::INT,
            'receiver_user_id' => ValueType::INT,
            'message' => ValueType::STRING,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $data, true);

        $messageDao = DAOFactory::getDmMessageDAO();
        $dmMessage = new DmMessage(
            message: $validatedData['message'],
            senderUserId: $validatedData['sender_user_id'],
            receiverUserId: $validatedData['receiver_user_id'],
            dmThreadId: $validatedData['dm_thread_id'],
        );

        $success = $messageDao->create($dmMessage);
        // TODO: try-catch
        if (!$success) throw new Exception('Failed to create a message!');

        $thread = $this->threads[$validatedData['dm_thread_id']] ?? null;
        //　スレッドが作成されている場合、receiver_user_idにだけメッセージを送る
        if (isset($thread)) {
            foreach (array_values($thread) as $client) {
                if ($client->getUserId() === $validatedData['receiver_user_id']) $client->getConn()->send($validatedData['message']);
            }
        }

    }
}
