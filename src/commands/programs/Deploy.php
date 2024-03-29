<?php

namespace src\commands\programs;

use src\commands\AbstractCommand;
use src\commands\Argument;

class Deploy extends AbstractCommand
{
    protected static ?string $alias = 'deploy';

    public static function getArguments(): array
    {
        return [];
    }

    public function execute(): int
    {
        $this->log('Starting to deploy...');
        $this->deploy();
        return 0;
    }

    public function deploy(): void
    {
        $this->log("Deploying...\n");

        shell_exec("git pull origin main");

        shell_exec("composer install --no-dev --optimize-autoloader");

        shell_exec("npm install --production");

        shell_exec("php console migrate");

        $this->log("Deployment completed successfully.\n");
    }
}
