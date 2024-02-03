<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class AddUpdatedAtColumnToPost implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE posts 
                ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                AFTER created_at;"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE posts 
                drop COLUMN updated_at;"
        ];
    }
}