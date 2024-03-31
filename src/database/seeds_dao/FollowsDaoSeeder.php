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

        for ($i = 1; $i <= SeedCount::USERS; $i++) {

            $countFollowing = $faker->numberBetween(SeedCount::MIN_FOLLOW, SeedCount::MAX_FOLLOW);
            $followingIds = [];

            for ($j = 0; $j < $countFollowing; $j++) {
                // 3分の1の確率でインフルエンサーをフォローする
                $followInfluencer = $faker->numberBetween(1, 3) === 1;
                do {
                    $followerUserId = $followInfluencer ? $faker->numberBetween(1, SeedCount::INFLUENCERS) : $faker->numberBetween(1, SeedCount::USERS);
                } while ($i === $followerUserId || in_array($followerUserId, $followingIds));

                $followingIds[] = $followerUserId;
                $follow = new Follow($i, $followerUserId);
                $data[] = $follow;
            }
        }

        return $data;
    }

    public function seedForProto(): void
    {
    }

    public function deleteAllEvents(): void
    {
    }
}
