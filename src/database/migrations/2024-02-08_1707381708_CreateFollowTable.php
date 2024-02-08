<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class CreateFollowTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE follows(
                following_user_id BIGINT NOT NULL,
                follower_user_id BIGINT NOT NULL,
                created_at DateTime DEFAULT current_timestamp NOT NULL,
                PRIMARY KEY(following_user_id, follower_user_id),
                FOREIGN KEY(following_user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(follower_user_id) REFERENCES users(id) ON DELETE CASCADE
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE follows;"
        ];
    }
}