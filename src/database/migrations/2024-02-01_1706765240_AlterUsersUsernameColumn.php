<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class AlterUsersUsernameColumn implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE users
                MODIFY COLUMN username VARCHAR(15) UNIQUE NOT NULL;"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE users
                MODIFY COLUMN username VARCHAR(255) UNIQUE;"
        ];
    }
}