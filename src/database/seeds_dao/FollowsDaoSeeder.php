<?php

namespace src\database\seeds_dao;

use PhpParser\Node\Stmt\Break_;
use src\database\data_access\DAOFactory;
use src\models\Follow;

class FollowsDaoSeeder implements Seeder
{
    public function seed(): void
    {
        $followDao = DAOFactory::getFollowDAO();
        $follows = $this->createDummyFollows();
        array_map(fn ($follow) => $followDao->create($follow), $follows);
    }

    public function createDummyFollows(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();
        $generatedPairs = [];

        for ($i = 1; $i <= SeedCount::USERS; $i++) {

            // 1人あたりフォローするのは10~400でランダム
            $countFollowing = $faker->numberBetween(SeedCount::MIN_FOLLOW, SeedCount::MAX_FOLLOW);

            for ($j = 0; $j < $countFollowing; $j++) {
                // 3分の1の確率で1から10のランダムな数字を選ぶ
                if (rand(1, 3) === 1) {
                    $followerUserId = $faker->numberBetween(1, SeedCount::INFLUENCERS);
                } else {
                    $followerUserId = $faker->numberBetween(1, SeedCount::USERS);
                }

                while ($i === $followerUserId || isset($generatedPairs[$i][$followerUserId])) {
                    if (rand(1, 3) === 1) {
                        $followerUserId = $faker->numberBetween(1, SeedCount::INFLUENCERS);
                    } else {
                        $followerUserId = $faker->numberBetween(1, SeedCount::USERS);
                    }
                }
                $generatedPairs[$i][$followerUserId] = true;

                $follow = new Follow($i, $followerUserId);
                $data[] = $follow;
            }
        }

        return $data;
    }
}
