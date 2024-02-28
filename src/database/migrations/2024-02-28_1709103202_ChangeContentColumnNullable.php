<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class ChangeContentColumnNullable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE posts 
                MODIFY content VARCHAR(280);"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE posts 
                MODIFY content VARCHAR(280) NOT NULL;"
        ];
    }
}