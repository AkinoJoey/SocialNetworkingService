<?php

namespace src\commands\programs;

use src\commands\AbstractCommand;
use src\database\data_access\DAOFactory;

class DeleteExpiredPasswordResetTokens extends AbstractCommand
{
    protected static ?string $alias = 'del-pass-toks';

    public static function getArguments(): array
    {
        return [];
    }

    public function execute(): int
    {
        $this->log("Starting to delete expired password reset tokens.....");
        $this->deleteExpired();
        return 0;
    }

    public function deleteExpired(): void
    {
        $passwordResetTokenDap = DAOFactory::getPasswordResetTokenDAO();
        $result = $passwordResetTokenDap->deleteExpired();

        if($result){
            $this->log('Delete successful.');
        }else{
            $this->log('Error occurred.');
        }
    }
}
