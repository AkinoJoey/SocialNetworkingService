<?php

namespace src\commands\programs;

use src\commands\AbstractCommand;
use src\database\data_access\DAOFactory;

class PostScheduled extends AbstractCommand
{
    protected static ?string $alias = 'po-sc';

    public static function getArguments(): array
    {
        return [];
    }

    public function execute(): int
    {
        $this->log("Starting a scheduled post.....");
        $this->postScheduled();
        return 0;
    }

    public function postScheduled() : void {
        $postDao = DAOFactory::getPostDAO();
        $result = $postDao->postScheduled();

        if($result){
            $this->log("Post successful.");
        }else{
            $this->log('Error occurred.');
        }
    }
}