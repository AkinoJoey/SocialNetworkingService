<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class AddUpdatedAtColumnToComment implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE comments 
                ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                AFTER created_at;"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE comments 
                drop COLUMN updated_at;"
        ];
    }
}