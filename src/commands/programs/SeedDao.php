<?php

namespace src\commands\programs;

use src\commands\AbstractCommand;

class SeedDao extends AbstractCommand
{
    protected static ?string $alias = 'seed-dao';

    public static function getArguments(): array
    {
        return [];
    }

    public function execute(): int
    {
        $this->runAllSeeds();
        return 0;
    }

    function runAllSeeds(): void
    {
        $directoryPath = __DIR__ . '/../../database/seeds_dao';

        $files = ['UsersDaoSeeder.php', 'ProfilesDaoSeeder.php', 'PostsDaoSeeder.php', 'PostLikesDaoSeeder.php', 'CommentsDaoSeeder.php', 'CommentLikesDaoSeeder.php', 'FollowsDaoSeeder.php'];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                // ファイル名からクラス名を抽出します。
                $className = 'src\database\seeds_dao\\' . pathinfo($file, PATHINFO_FILENAME);

                // シードファイルをインクルードします。
                include_once $directoryPath . '/' . $file;

                if (class_exists($className)) {
                    $seeder = new $className();
                    $seeder->seed();
                } else throw new \Exception("$className does not exist.");
            }
        }
    }
}
