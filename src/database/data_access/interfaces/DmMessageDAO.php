<?php 

namespace src\database\data_access\interfaces;

use src\models\DmMessage;

interface DmMessageDAO{
    public function create(DmMessage $dmMessage): bool;
    public function delete(int $id) : bool;
    public function getById(int $id) : DmMessage;
    public function getOneHundredByDmThreadId(int $dmThreadId) : array;
    public function getMessageList(int $userId, int $limit = 100): array;
}