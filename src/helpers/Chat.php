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
    protected $threads = []; //[thread_id => [user_id1, user_id2]]
    protected $clients = []; // [user_id => connection]

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

        switch($data['type']){
            case 'start':
                $required_fields = [
                    'dm_thread_id' => ValueType::INT,
                    'sender_user_id' => ValueType::INT,
                    'receiver_user_id' => ValueType::INT,
                ];

                $validatedData = ValidationHelper::validateFields($required_fields, $data, true);

                if(!isset($threads[$validatedData['thread_id']])){
                    $this->threads[$validatedData['thread_id']] = [$validatedData['sender_user_id'], $validatedData['receiver_user_id']];
                    $this->clients[$validatedData['sender_user_id']] = $from;
                }
                
                break;

            case 'message':
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


                foreach ($this->clients as $client) {
                    if ($from !== $client) {
                        // The sender is not the receiver, send to each client connected
                        $client->send($msg);
                    }
                }

        }

        
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
