<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class CreatePostLikeNotification implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE post_like_notifications(
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT NOT NULL,
                post_id BIGINT NOT NULL,
                is_read BOOLEAN,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE post_like_notifications;"
        ];
    }
}