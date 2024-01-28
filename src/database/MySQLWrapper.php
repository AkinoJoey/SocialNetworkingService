<?php

namespace src\database;

use Exception;
use mysqli;
use src\helpers\Settings;

class MySQLWrapper extends mysqli
{
    public function __construct(?string $hostname = 'localhost', ?string $username = null, ?string $password = null, ?string $database = null, ?int $port = null, ?string $socket = null)
    {
        /*
            接続の失敗時にエラーを報告し、例外をスローします。データベース接続を初期化する前にこの設定を行ってください。
            テストするには、.env設定で誤った情報を入力します。
        */
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $username = $username ?? Settings::env('DATABASE_USER');
        $password = $password ?? Settings::env('DATABASE_USER_PASSWORD');
        $database = $database ?? Settings::env('DATABASE_NAME');

        parent::__construct($hostname, $username, $password, $database, $port, $socket);
    }

    // クエリが問い合わせられるデフォルトのデータベースを取得します。
    // エラーは失敗時にスローされます（つまり、クエリがfalseを返す、または取得された行がない）
    // これらに対処するためにifブロックやcatch文を使用することもできます。
    public function getDatabaseName(): string
    {
        return $this->query("SELECT database() AS the_db")->fetch_row()[0];
    }

    public function getUserName(): string
    {
        return $this->query("SELECT SUBSTRING_INDEX(USER(), '@', 1) AS the_user")->fetch_row()[0];
    }

    public function prepareAndFetchAll(string $prepareQuery, string $types, array $data): ?array
    {
        $this->typesAndDataValidationPass($types, $data);

        $stmt = $this->prepare($prepareQuery);
        if (count($data) > 0) $stmt->bind_param($types, ...$data);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result === false) throw new Exception(sprintf('Error fetching data on query %s', $prepareQuery));

        // 連想モードを使用して、列名も取得します。
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function prepareAndExecute(string $prepareQuery, string $types, array $data): bool
    {
        $this->typesAndDataValidationPass($types, $data);

        $stmt = $this->prepare($prepareQuery);
        if (count($data) > 0) $stmt->bind_param($types, ...$data);
        return $stmt->execute();
    }

    private function typesAndDataValidationPass(string $types, array $data): void
    {
        if (strlen($types) !== count($data)) throw new Exception(sprintf('Type and data must equal in length %s vs %s', strlen($types), count($data)));
    }
}