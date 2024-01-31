<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class CreateProfileTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS profiles (
                id BIGINT PRIMARY KEY AUTO_INCREMENT,
                age INT,
                location VARCHAR(255),
                description VARCHAR(255),
                profile_image_path VARCHAR(255),
                user_id BIGINT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
            )"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE profiles"
        ];
    }
}