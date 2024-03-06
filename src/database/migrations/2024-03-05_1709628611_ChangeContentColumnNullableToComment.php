<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class ChangeContentColumnNullableToComment implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE comments 
                MODIFY content VARCHAR(280);"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE comments 
                MODIFY content VARCHAR(280) NOT NULL;"
        ];
    }
}