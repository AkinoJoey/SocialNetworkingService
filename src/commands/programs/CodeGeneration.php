<?php

namespace src\commands\programs;

use src\commands\AbstractCommand;
use src\commands\Argument;

class CodeGeneration extends AbstractCommand
{
    // 使用するコマンド名を設定します
    protected static ?string $alias = 'code-gen';
    protected static bool $requiredCommandValue = true;

    // 引数を割り当てます
    public static function getArguments(): array
    {
        return [
            (new Argument('name'))->description('Name of the file that is to be generated.')->required(false),
        ];
    }

    public function execute(): int
    {
        $codeGenType = $this->getCommandValue();
        $this->log('Generating code for.......' . $codeGenType);

        if ($codeGenType === 'migration') {
            $migrationName = $this->getArgumentValue('name');
            $this->generateMigrationFile($migrationName);
        } elseif ($codeGenType === 'seeder') {
            $className = $this->getArgumentValue('name');
            $this->generateSeederFile($className);
        }else{
            $this->log(sprintf("error: %s type does not exist.", $codeGenType));
        }
        return 0;
    }

    private function generateMigrationFile(string $migrationName): void
    {
        $filename = sprintf(
            '%s_%s_%s.php',
            date('Y-m-d'),
            time(),
            $migrationName
        );

        $migrationContent = $this->getMigrationContent($migrationName);

        // 移行ファイルを保存するパスを指定します
        $path = sprintf("%s/../../Database/migrations/%s", __DIR__, $filename);

        file_put_contents($path, $migrationContent);
        $this->log("Migration file {$filename} has been generated!");
    }

    private function getMigrationContent(string $migrationName): string
    {
        $className = $this->pascalCase($migrationName);

        return <<<MIGRATION
<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class {$className} implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [];
    }
}
MIGRATION;
    }

    private function pascalCase(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    private function generateSeederFile(string $className): void
    {
        $className = $this->pascalCase($className);
        $className = preg_match('/.+Seeder/', $className) ? $className : $className . 'Seeder';
        $filename = $className . '.php';

        $seederContent = $this->getSeedContent($className);

        $path = sprintf("%s/../../database/Seeds/%s", __DIR__, $filename);

        file_put_contents($path, $seederContent);
        $this->log("Seeder file {$className} has been generated!");
    }

    private function getSeedContent(string $className): string
    {


        return sprintf(<<<'EOD'
        <?php

        namespace Database\Seeds;

        use Database\AbstractSeeder;

        class %s extends AbstractSeeder {

            // TODO: tableName文字列を割り当ててください。
            protected ?string $tableName = null;

            // TODO: tableColumns配列を割り当ててください。
            protected array $tableColumns = [];

            public function createRowData(): array
            {
                // TODO: createRowData()メソッドを実装してください。
                return [];
            }
        }
        EOD, $className);
    }
}
