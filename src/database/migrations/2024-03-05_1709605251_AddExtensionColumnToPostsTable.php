<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class AddExtensionColumnToPostsTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE posts 
                ADD COLUMN extension VARCHAR(5) AFTER media_path;"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE posts 
                DROP COLUMN extension;"
        ];
    }
}