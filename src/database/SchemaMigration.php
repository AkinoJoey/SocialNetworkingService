<?php

namespace src\database;

interface SchemaMigration
{
    public function up(): array;
    public function down(): array;
}
