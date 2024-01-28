<?php

namespace src\database;

interface Seeder
{
    public function seed(): void;

    public function createRowData(): array;
}
