<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class CreateDmThreadTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE dm_threads(
                id BIGINT PRIMARY KEY AUTO_INCREMENT,
                url VARCHAR(18) NOT NULL UNIQUE,
                user_id1 BIGINT NOT NULL,
                user_id2 BIGINT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id1) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(user_id2) REFERENCES users(id) ON DELETE CASCADE
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE dm_threads;"
        ];
    }
}