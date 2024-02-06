<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class CreatePostLikeTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE post_likes(
                user_id BIGINT NOT NULL,
                post_id BIGINT NOT NULL,
                created_at DateTime default CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY(user_id, post_id),
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE CASCADE
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE post_likes;"
        ];
    }
}