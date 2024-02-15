<?php 

namespace src\helpers;
use Ratchet\ConnectionInterface;

class ChatClient{


    public function __construct(
        private ConnectionInterface $conn,
        private int $userId,
        private int $joinedThreadId
    ) {
    }

    public function getConn() : ConnectionInterface {
        return $this->conn;
    }

    public function setConn(ConnectionInterface $conn) : void {
        $this->conn = $conn;
    }

    public function getUserId() : int {
        return $this->userId;
    }

    public function setUserId(int $userId) : void {
        $this->userId = $userId;
    }

    public function getJoinedThreadId() : int {
        return $this->joinedThreadId;
    }

    public function setJoinedThreadId(int $threadId) : void {
        $this->joinedThreadId = $threadId;
    }
}