<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class AddStatusColumnToPost implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE posts
                ADD COLUMN status VARCHAR(9) NOT NULL AFTER scheduled_at;"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE posts
                DROP COLUMN status;"
        ];
    }
}