<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class CreateNotificationsTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE notifications(
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT NOT NULL,
                notification_type VARCHAR(30) NOT NULL,
                related_id BIGINT NOT NULL,
                is_read BOOLEAN NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE notifications;"
        ];
    }
}