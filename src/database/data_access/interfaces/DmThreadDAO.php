<?php 

namespace src\database\data_access\interfaces;

use src\models\DmThread;

interface DmThreadDAO{
    public function create(DmThread $dmThread): bool;
    public function getByUserIds(int $userId1, int $userId2): ?DmThread;
    public function getByUserIdAndUrl(int $userId, string $url): ?DmThread;
}