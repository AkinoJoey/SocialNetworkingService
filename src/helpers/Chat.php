<?php

namespace src\helpers;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use src\types\ValueType;
use src\helpers\ValidationHelper;
use src\database\data_access\DAOFactory;
use src\models\DmMessage;
use Exception;

class Chat implements MessageComponentInterface
{
    protected $threads = []; // [threadId => [userId1 => userId1, userId2=>userId2]]
    protected $clients = []; // [userId(resourceId) => [conn => conn, joinedThreads => [threads_id ...]]]

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
        // threadから削除
        $user_id = $conn->resourceId;


        // threadが空の場合はthreadを削除
        if (count($thread) < 1) unset($thread);

        // clientsから削除
        unset($this->clients[$validatedData['sender_user_id']]);
        var_dump($this->threads);

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

        $from->resourceId = $validatedData['sender_user_id'];

        if(!isset($this->clients[$validatedData['sender_user_id']])){
            $this->clients[$validatedData['sender_user_id']] = ['conn' => $from];
        }

        $this->clients[$validatedData['sender_user_id']]['joinedThreads'][$validatedData['dm_thread_id']] =  $validatedData['dm_thread_id'];


        $this->threads[$validatedData['dm_thread_id']][$validatedData['sender_user_id']] = [$validatedData['sender_user_id']];

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

        $thread = isset($this->threads[$validatedData['dm_thread_id']]) ? $this->threads[$validatedData['dm_thread_id']] : null;
        //　スレッドが作成されていて、かつreceiver_user_idが存在するときに、receiver_user_idにメッセージを送る
        if (isset($thread) && isset($thread[$validatedData['receiver_user_id']])) {
            $this->clients[$validatedData['receiver_user_id']]->send(json_encode($validatedData));
        }


    }

    private function leave(array $data): void
    {
        // TODO: error handling
        $required_fields = [
            'dm_thread_id' => ValueType::INT,
            'sender_user_id' => ValueType::INT,
            'receiver_user_id' => ValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $data, true);

        

    }
}
