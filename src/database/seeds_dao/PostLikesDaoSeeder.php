<?php

namespace src\database\seeds_dao;

use src\database\data_access\DAOFactory;
use src\models\PostLike;

class PostLikesDaoSeeder implements Seeder
{
    public function seed(): void
    {
        $postLikeDao = DAOFactory::getPostLikeDAO();
        $postLikes = $this->createDummyPostLikes();
        array_map(fn ($postLike) => $postLikeDao->create($postLike), $postLikes);
    }

    public function createDummyPostLikes(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();
        $generatedPairs = [];

        for ($i = 0; $i < SeedCount::POST_LIKES; $i++) {
            do {
                $userId = $faker->numberBetween(1, SeedCount::USERS);
                $postId = $faker->numberBetween(1, SeedCount::POSTS);
                $pair = $userId . '_' . $postId;
            } while (isset($generatedPairs[$pair])); // すでに生成されたペアかどうかをチェック

            // 生成されたペアをトラッキング
            $generatedPairs[$pair] = true;

            $postLike = new PostLike(
                userId: $userId,
                postId: $postId
            );

            $data[] = $postLike;
        }

        return $data;
    }
}
