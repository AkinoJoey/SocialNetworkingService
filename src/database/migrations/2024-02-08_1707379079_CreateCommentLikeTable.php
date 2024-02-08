<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class CreateCommentLikeTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE comment_likes(
                user_id BIGINT NOT NULL,
                comment_id BIGINT NOT NULL,
                created_at DateTime default CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY(user_id, comment_id),
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(comment_id) REFERENCES comments(id) ON DELETE CASCADE
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE comment_likes;"
        ];
    }
}
