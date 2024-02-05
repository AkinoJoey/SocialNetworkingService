<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class CreateCommentTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS comments(
                id BIGINT PRIMARY KEY AUTO_INCREMENT,
                content VARCHAR(280) NOT NULL,
                url VARCHAR(18) NOT NULL,
                media_path VARCHAR(255) unique,
                user_id BIGINT NOT NULL,
                post_id BIGINT,
                parent_comment_id BIGINT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                foreign key(user_id) REFERENCES users(id) ON DELETE CASCADE,
                foreign key(post_id) REFERENCES posts(id) ON DELETE CASCADE,
                foreign key(parent_comment_id) REFERENCES comments(id) ON DELETE CASCADE
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE comments;"
        ];
    }
}