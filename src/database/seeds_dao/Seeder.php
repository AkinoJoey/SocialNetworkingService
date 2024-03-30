<?php

namespace src\database\seeds_dao;


interface Seeder
{
    public function seed() : void;
    public function seedForProto() : void;
    public function deleteAllEvents() : void;
}
