<?php

namespace src\commands\programs;

use src\commands\AbstractCommand;

class SeedDao extends AbstractCommand
{
    protected static ?string $alias = 'seed-dao';
    protected static bool $requiredCommandValue = true;

    public static function getArguments(): array
    {
        return [];
    }

    public function execute(): int
    {
        $seedType = $this->getCommandValue();

        if ($seedType === 'init') {
            $this->seedsForInit();
        } elseif ($seedType === 'proto') {
            $this->log("Seeding prototype data.....");
            $result = $this->seedsForPrototype();
            if($result){
                $this->log('Seeding successful.');
            }else{
                $this->log('Error occurred.');
            }

        }  elseif($seedType === 'deleteEvents'){
            $this->deleteEventSeeds();
        }else {
            $this->log(sprintf("error: %s type does not exist.", $seedType));
        }
        return 0;
    }

    function seedsForInit(): bool
    {
        $files = ['UsersDaoSeeder.php', 'ProfilesDaoSeeder.php', 'PostsDaoSeeder.php', 'PostLikesDaoSeeder.php', 'CommentsDaoSeeder.php', 'CommentLikesDaoSeeder.php', 'FollowsDaoSeeder.php'];
        $this->runByType($files, 'init');
        return true;
    }

    public function seedsForPrototype(): bool
    {
        $files = ['PostsDaoSeeder.php', 'PostLikesDaoSeeder.php', 'CommentsDaoSeeder.php'];
        $this->runByType($files, 'proto');

        return true;
    }

    private function runByType(array $files, String $seedType): void
    {

        $directoryPath = __DIR__ . '/../../database/seeds_dao';

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                // ファイル名からクラス名を抽出します。
                $className = 'src\database\seeds_dao\\' . pathinfo($file, PATHINFO_FILENAME);

                // シードファイルをインクルードします。
                include_once $directoryPath . '/' . $file;

                if (class_exists($className)) {
                    $seeder = new $className();
                    if ($seedType === 'init') {
                        $seeder->seed();
                    } else if ($seedType === 'proto') {
                        $seeder->seedForProto();
                    }else if($seedType === 'delete'){
                        $seeder->deleteAllEvents();
                    } else {
                        throw new \Exception("$seedType does not exist.");
                    }
                } else throw new \Exception("$className does not exist.");
            }
        }
    }

    private function deleteEventSeeds(): bool
    {
        $files = ['PostsDaoSeeder.php', 'PostLikesDaoSeeder.php', 'CommentsDaoSeeder.php'];

        $this->runByType($files, 'delete');

        return true;
    }
}
