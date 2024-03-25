<?php

namespace src\database\seeds_dao;

use src\database\data_access\DAOFactory;
use src\models\Follow;

class FollowsDaoSeeder implements Seeder
{
    public function seed(): void
    {
        $followDao = DAOFactory::getFollowDAO();
        $follows = $this->createDummyFollows();
        array_map(fn($follow)=>$followDao->create($follow), $follows);
    }

    public function createDummyFollows(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();

        // ユーザー数
        $numberOfUsers = 20;
        // インフルエンサーの数(id 1-5)
        $numberOfInfluencers = 5;

        $generatedPairs = [];

        for ($i = 1; $i <= $numberOfUsers; $i++) {
            
            // 1人あたりフォローするのは3~10でランダム
            $countFollowing = $faker->numberBetween(3, 10);
            for ($j = 0; $j < $countFollowing; $j++) {
                // インフルエンサーをフォローする確率は1/5
                $isFollowingInfluencer = rand(1, 5) === 1;
                if ($isFollowingInfluencer) {
                    do{
                        $followerUserId = $faker->numberBetween(1, $numberOfInfluencers);
                    }while($i === $followerUserId || isset($generatedPairs[$i][$followerUserId]));
                } else {
                    // ユーザー同士をフォロー
                    do {
                        $followerUserId = $faker->numberBetween(1, $numberOfUsers);
                    } while ($i === $followerUserId || isset($generatedPairs[$i][$followerUserId]));
                }

                $generatedPairs[$i][$followerUserId] = true;

                $follow = new Follow($i, $followerUserId);
                $data[] = $follow;
            }
        }

        return $data;
    }

}
