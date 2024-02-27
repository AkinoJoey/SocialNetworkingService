<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class AddUpdatedAtColumnToNotification implements SchemaMigration
{
    public function up(): array
    {
        return [
            "
            ALTER TABLE notifications 
                ADD updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
            "
        ];
    }

    public function down(): array
    {
        return [
            "
            ALTER TABLE notifications 
                DROP COLUMN updated_at;
            "
        ];
    }
}