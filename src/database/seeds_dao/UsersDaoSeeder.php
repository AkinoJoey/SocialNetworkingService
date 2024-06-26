<?php

namespace src\database\seeds_dao;

use src\database\data_access\DAOFactory;
use src\models\User;

class UsersDaoSeeder implements Seeder
{
    public function seed(): void
    {
        $userDao = DAOFactory::getUserDAO();
        $users = $this->createDummyUsers();
        array_map(fn ($userMap) => $userDao->create($userMap['user'], $userMap['password']), $users);
    }

    public function createDummyUsers(): array
    {

        $faker = \Faker\Factory::create();
        $data = [];

        for ($i = 0; $i < SeedCount::USERS; $i++) {
            $user = new User(
                accountName: $faker->name(),
                email: $faker->unique()->email(),
                username: uniqid(),
                emailVerified: true,
            );

            $password = $faker->password(10, 30);
            $data[] = ['user' => $user, 'password' => $password];
        }

        return $data;
    }
    
    public function seedForProto(): void
    {
    }

    public function deleteAllEvents(): void{
        
    }
}
