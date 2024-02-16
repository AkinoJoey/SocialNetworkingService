<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class AddIvColumnToDmMessageTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE dm_messages 
                ADD COLUMN iv BLOB NOT NULL AFTER message;"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE dm_messages 
                DROP COLUMN iv;"
        ];
    }
}