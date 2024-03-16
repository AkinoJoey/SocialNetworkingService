<?php

namespace src\database\migrations;

use src\database\SchemaMigration;

class AddExtensionColumnToProfile implements SchemaMigration
{
    public function up(): array
    {
        return [
            "ALTER TABLE profiles
                ADD COLUMN extension VARCHAR(5) AFTER profile_image_path;"
        ];
    }

    public function down(): array
    {
        return [
            "ALTER TABLE profiles
                DROP COLUMN extension;"
        ];
    }
}