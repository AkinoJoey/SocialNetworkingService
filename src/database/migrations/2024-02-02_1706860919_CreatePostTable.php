<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class CreatePostTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS posts (
                id BIGINT PRIMARY KEY AUTO_INCREMENT,
                content VARCHAR(280) NOT NULL,
                media_path VARCHAR(255),
                scheduled_at DATETIME DEFAULT NULL,
                user_id BIGINT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
            );
            "
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE posts"
        ];
    }
}