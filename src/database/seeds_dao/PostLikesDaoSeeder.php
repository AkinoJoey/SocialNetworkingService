<?php

namespace src\database\seeds_dao;

use DateTime;
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

    public function seedForProto(): void
    {
        $postLikeDao = DAOFactory::getPostLikeDAO();
        // $randomPostLikes = $this->createPostLikesForProto();
        $postLikes = $this->createPostLikesForInfluencers();
        // $postLikes = array_merge($randomPostLikes, $postLikesForInfluencer);
        $faker = \Faker\Factory::create();
        date_default_timezone_set('Asia/Tokyo');

        $now = new DateTime();
        $today = new DateTime('today');

        for ($i = 0; $i < count($postLikes); $i++) {
            $postLike = $postLikes[$i];
            $executeAt = $faker->dateTimeBetween($now->format('Y-m-d H:i:s'), $today->format('Y-m-d 23:59:59'))->format('Y-m-d H:i:s');
            $postLikeDao->createForProto($i + 1, $executeAt, $postLike);
        }
    }

    private function createPostLikesForProto(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();
        $generatedPairs = [];

        $postDao = DAOFactory::getPostDAO();
        $numberOfPosts = $postDao->count();

        $postLikeDao = DAOFactory::getPostLikeDAO();
        $influencerPostsIds = $postDao->getInfluencerPostIds();

        for ($i = 0; $i < SeedCount::USERS; $i++) {
            $userId = $i + 1;

            for ($j = 0; $j < SeedCount::POST_LIKES_FOR_PROTO + SeedCount::POST_LIKES_FOR_INFLUENCER; $j++) {
                // 他のユーザーから 5 つのランダムな投稿に「いいね」する
                if ($j < SeedCount::POST_LIKES_FOR_PROTO) {
                    do {
                        $userId = $faker->numberBetween(1, SeedCount::USERS);
                        $postId = $faker->numberBetween(1, $numberOfPosts);
                        $pair = $userId . '_' . $postId;
                    } while ($i === $userId || isset($generatedPairs[$pair]) || $postLikeDao->exists($userId, $postId)); // すでに生成されたペアかどうかをチェック
                }else{
                    //　 50 人の「インフルエンサー」アカウントの中から 20 の投稿に「いいね」をする
                    do {
                        $userId = $faker->numberBetween(1, SeedCount::USERS);
                        $postId = $influencerPostsIds[$faker->numberBetween(0, count($influencerPostsIds)-1)];
                        $pair = $userId . '_' . $postId;
                    } while (isset($generatedPairs[$pair]) || $postLikeDao->exists($userId, $postId)); 
                }

                // 生成されたペアをトラッキング
                $generatedPairs[$pair] = true;

                $postLike = new PostLike(
                    userId: $userId,
                    postId: $postId
                );

                $data[] = $postLike;
            }
        }

        return $data;
    }

    public function deleteAllEvents(): void
    {
        $postLikesDao = DAOFactory::getPostLikeDAO();
        for ($i = 0; $i < SeedCount::USERS * SeedCount::POST_LIKES_FOR_PROTO; $i++) {
            $eventName = 'random_post_like_' . ($i + 1);
            $postLikesDao->deleteEvent($eventName);
        }
    }

    public function createPostLikesForInfluencers(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();
        $generatedPairs = [];

        $postDao = DAOFactory::getPostDAO();
        $influencerPostsIds = $postDao->getInfluencerPostIds();

        $postLikeDao = DAOFactory::getPostLikeDAO();

        for ($i = 0; $i < SeedCount::USERS; $i++) {
            $userId = $i + 1;

            for ($j = 0; $j < SeedCount::POST_LIKES_FOR_INFLUENCER; $j++) {
                do {
                    $userId = $faker->numberBetween(1, SeedCount::USERS);
                    $postId = $influencerPostsIds[$faker->numberBetween(0, count($influencerPostsIds)-1)];
                    $pair = $userId . '_' . $postId;
                } while (isset($generatedPairs[$pair]) || $postLikeDao->exists($userId, $postId)); // すでに生成されたペアかどうかをチェック

                // 生成されたペアをトラッキング
                $generatedPairs[$pair] = true;

                $postLike = new PostLike(
                    userId: $userId,
                    postId: $postId
                );

                $data[] = $postLike;
            }
        }

        return $data;
    }
}
