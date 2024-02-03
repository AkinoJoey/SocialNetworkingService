<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class AddUrlColumnToPost implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE posts 
                ADD COLUMN url VARCHAR(18) NOT NULL UNIQUE
                AFTER content;"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE posts 
                DROP COLUMN url;"
        ];
    }
}