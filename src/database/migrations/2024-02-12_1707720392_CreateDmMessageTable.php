<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class CreateDmMessageTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE table dm_messages(
                id BIGINT PRIMARY KEY AUTO_INCREMENT,
                text VARCHAR(10000) NOT NULL,
                sender_user_id BIGINT NOT NULL,
                receiver_user_id BIGINT NOT NULL,
                dm_thread_id BIGINT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(sender_user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(receiver_user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(dm_thread_id) REFERENCES dm_threads(id) ON DELETE CASCADE
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE dm_messages"
        ];
    }
}