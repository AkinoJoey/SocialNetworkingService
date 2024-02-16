<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class ChangeEmailVerifiedColumnToBool implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE users
                MODIFY COLUMN email_verified BOOLEAN NOT NULL;"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE users 
                MODIFY COLUMN email_verified VARCHAR(255);"
        ];
    }
}