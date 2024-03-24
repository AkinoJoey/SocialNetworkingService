<?php

namespace src\database\seeds_dao;

use src\database\data_access\DAOFactory;
use src\models\Profile;

class ProfileDaoSeeder
{
    public function seed(): void
    {
        $profileDao = DAOFactory::getProfileDAO();
        $profiles = $this->createDummyProfiles();
        array_map(fn($profile)=> $profileDao->create($profile), $profiles);
    }

    public function createDummyProfiles(): array
    {

        $faker = \Faker\Factory::create();
        $data = [];
        $numberOfDummyUsers  = 20;

        for ($i = 0; $i < $numberOfDummyUsers; $i++) {
            $profile = new Profile(
                userId: $i + 1,
                age: $faker->numberBetween(20, 60),
                location: $faker->country(),
                description: $faker->realTextBetween(10, 140)
            );

            $data[] = $profile;
        }

        return $data;
    }
}
