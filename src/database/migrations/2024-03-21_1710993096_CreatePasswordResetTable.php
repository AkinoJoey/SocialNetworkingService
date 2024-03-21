<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class CreatePasswordResetTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE password_reset_tokens (
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT NOT NULL UNIQUE,
                token VARBINARY(32) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE password_reset_tokens;"
        ];
    }
}