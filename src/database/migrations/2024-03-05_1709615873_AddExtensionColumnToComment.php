<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class AddExtensionColumnToComment implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE comments 
                ADD COLUMN extension VARCHAR(5) AFTER media_path;"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE comments 
                DROP COLUMN extension;"
        ];
    }
}