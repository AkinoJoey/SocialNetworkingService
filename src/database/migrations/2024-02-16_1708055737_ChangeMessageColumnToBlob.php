<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class ChangeMessageColumnToBlob implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE dm_messages 
                MODIFY COLUMN message BLOB NOT NULL;"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE dm_messages 
                MODIFY COLUMN message VARCHAR(10000) NOT NULL;"
        ];
    }
}