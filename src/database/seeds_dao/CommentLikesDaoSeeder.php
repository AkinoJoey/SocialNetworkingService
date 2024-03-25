<?php

namespace src\database\seeds_dao;

use src\database\data_access\DAOFactory;
use src\models\CommentLike;

class CommentLikesDaoSeeder implements Seeder
{
    public function seed(): void
    {
        $commentLikeDao = DAOFactory::getCommentLikeDAO();
        $commentLikes = $this->createDummyCommentLikes();
        array_map(fn ($commentLike) => $commentLikeDao->create($commentLike), $commentLikes);
    }

    public function createDummyCommentLikes(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();

        $numberOfDummyCommentLikes = 300;

        $generatedPairs = [];

        for ($i = 0; $i < $numberOfDummyCommentLikes; $i++) {
            do {
                $userId = $faker->numberBetween(1, 20);
                $commentId = $faker->numberBetween(1, 100);
                $pair = $userId . '_' . $commentId;
            } while (isset($generatedPairs[$pair])); // すでに生成されたペアかどうかをチェック

            // 生成されたペアをトラッキング
            $generatedPairs[$pair] = true;

            $commentLike = new CommentLike(
                userId: $userId,
                commentId: $commentId
            );

            $data[] = $commentLike;
        }

        return $data;
    }
}
