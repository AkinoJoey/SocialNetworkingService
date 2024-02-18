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
                source_id BIGINT NOT NULL,
                notification_type VARCHAR(30) NOT NULL,
                post_id BIGINT, 
                comment_id BIGINT,
                dm_thread_id BIGINT,
                is_read BOOLEAN NOT NULL DEFAULT FALSE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(source_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY(comment_id) REFERENCES comments(id) ON DELETE CASCADE,
                FOREIGN KEY(dm_thread_id) REFERENCES dm_threads(id) ON DELETE CASCADE
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
