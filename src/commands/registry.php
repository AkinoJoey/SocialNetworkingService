<?php
return [
    src\commands\programs\Migrate::class,
    src\commands\programs\CodeGeneration::class,
    src\commands\programs\Seed::class,
    src\commands\programs\SeedDao::class,
    src\commands\programs\CommandGeneration::class,
    src\commands\programs\PostScheduled::class,
    src\commands\programs\DeleteExpiredPasswordResetTokens::class,
    src\commands\programs\DeleteExpiredEmailVerificationUsers::class,
];
