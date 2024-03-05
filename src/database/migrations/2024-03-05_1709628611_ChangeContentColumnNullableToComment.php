<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class ChangeContentColumnNullableToComment implements SchemaMigration
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