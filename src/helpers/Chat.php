<?php

namespace src\helpers;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use src\helpers\ValidationHelper;
use Exception;
use src\helpers\ChatClient;
use src\types\GeneralValueType;
use src\types\PostValueType;

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
        try {
            $data = json_decode($msg, true);
            $validatedType = ValidationHelper::chatType($data['type']);

            switch ($validatedType) {
                case 'join':
                    $this->join($from, $data);
                    break;

                case 'message':
                    $this->sendMessage($from, $data);
                    break;
            }
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            $from->send(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        } catch (\LengthException $e) {
            error_log($e->getMessage());
            $from->send(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        } catch (Exception $e) {
            error_log( $e->getMessage());
            $from->send(json_encode(['status' => 'error', 'message' => 'エラーが発生しました']));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        if (isset($this->clients[$conn->resourceId])) {
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
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "エラーが発生しました: {$e->getMessage()}\n";
    }

    private function join(ConnectionInterface $from, array $data): void
    {
        try {
            $required_fields = [
                'dm_thread_id' => GeneralValueType::INT,
                'sender_user_id' => GeneralValueType::INT,
                'receiver_user_id' => GeneralValueType::INT,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $data);

            $client = new ChatClient($from, $validatedData['sender_user_id'], $validatedData['dm_thread_id']);

            $this->clients[$from->resourceId] = $client;
            $this->threads[$validatedData['dm_thread_id']][$from->resourceId] = $client;

            echo "Starting chat! ({$from->resourceId})\n";
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            $from->send(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        } catch (Exception $e) {
            error_log( $e->getMessage());
            $from->send(json_encode(['status' => 'error', 'message' => 'エラーが発生しました']));
        }
    }

    private function sendMessage(ConnectionInterface $from, array $data): void
    {
        try {
            $required_fields = [
                'dm_thread_id' => GeneralValueType::INT,
                'sender_user_id' => GeneralValueType::INT,
                'receiver_user_id' => GeneralValueType::INT,
                'message' => PostValueType::CONTENT //最大文字数は140文字
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $data);

            ChatHelper::createMessage($validatedData['message'], $validatedData['sender_user_id'], $validatedData['receiver_user_id'], $validatedData['dm_thread_id']);

            $thread = $this->threads[$validatedData['dm_thread_id']] ?? null;
            //　スレッドが作成されている場合、receiver_user_idにだけメッセージを送る
            if (isset($thread)) {
                foreach (array_values($thread) as $client) {
                    if ($client->getUserId() === $validatedData['receiver_user_id']) {

                        $data = json_encode(['status' => 'success', 'message' => $validatedData['message']]);
                        $client->getConn()->send($data);
                    } else {

                        // リアルタイム通信しない場合、DMの未読通知がない場合はNotificationを作成する。
                        // 作成済みのNotificationが存在する場合はupdatedAtを更新する
                        ChatHelper::createOrUpdateNotification($validatedData['receiver_user_id'], $validatedData['sender_user_id'], $validatedData['dm_thread_id']);
                    }
                }
            }
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            $from->send(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        } catch (\LengthException $e) {
            error_log($e->getMessage());
            $from->send(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        } catch (Exception $e) {
            error_log($e->getMessage());
            $from->send(json_encode(['status' => 'error', 'message' => 'エラーが発生しました']));
        }
    }
}
