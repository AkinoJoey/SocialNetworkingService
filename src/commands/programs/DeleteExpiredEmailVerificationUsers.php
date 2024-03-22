<?php

namespace src\commands\programs;

use src\commands\AbstractCommand;
use src\database\data_access\DAOFactory;

class DeleteExpiredEmailVerificationUsers extends AbstractCommand
{
    protected static ?string $alias = 'del-exp-users';

    public static function getArguments(): array
    {
        return [];
    }

    public function execute(): int
    {
        $this->log("Starting to delete expired email verification users.....");
        $this->deleteExpiredEmailVerificationUsers();
        return 0;
    }

    public function deleteExpiredEmailVerificationUsers(): void
    {
        $userDao = DAOFactory::getUserDAO();
        $result = $userDao->deleteExpiredEmailVerificationUsers();

        if ($result) {
            $this->log('Delete successful.');
        } else {
            $this->log('Error occurred.');
        }
    }
}
