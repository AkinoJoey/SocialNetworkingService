<?php

namespace src\database\seeds_dao;

use src\database\data_access\DAOFactory;
use src\models\Comment;
use src\models\DataTimeStamp;
use DateTime;

class CommentsDaoSeeder implements Seeder
{
    public function seed(): void
    {
        $commentDao = DAOFactory::getCommentDAO();
        $comments = $this->createDummyComments(); //ポストへのコメント
        $childComments = $this->createChildComments(); // コメントへのコメント
        array_map(fn ($comment) => $commentDao->create($comment), $comments);
        array_map(fn ($comment) => $commentDao->create($comment), $childComments);
    }

    public function createDummyComments(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();
        //投稿のURLとメディアのファイル名の長さ 
        $numberOfCharacters = 18;

        for ($i = 0; $i < SeedCount::COMMENTS; $i++) {
            $comment = new Comment(
                url: bin2hex(random_bytes($numberOfCharacters / 2)),
                userId: $faker->numberBetween(1, SeedCount::USERS),
                content: $faker->realTextBetween(10, 140, 5),
                postId: $faker->numberBetween(1, SeedCount::POSTS),
                timeStamp: new DataTimeStamp($faker->dateTimeBetween('-1 years')->format('Y-m-d H:i:s'), (new DateTime())->format("Y-m-d H:i:s")),
            );

            $data[] = $comment;
        }

        return $data;
    }

    public function createChildComments(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();
        //投稿のURLとメディアのファイル名の長さ 
        $numberOfCharacters = 18;

        for ($i = 0; $i < SeedCount::CHILD_COMMENTS; $i++) {
            $comment = new Comment(
                url: bin2hex(random_bytes($numberOfCharacters / 2)),
                userId: $faker->numberBetween(1, SeedCount::USERS),
                content: $faker->realTextBetween(10, 140, 5),
                parentCommentId: $faker->numberBetween(1, SeedCount::COMMENTS),
                timeStamp: new DataTimeStamp($faker->dateTimeBetween('-1 years')->format('Y-m-d H:i:s'), (new DateTime())->format("Y-m-d H:i:s")),
            );

            $data[] = $comment;
        }

        return $data;
    }

    public function seedForProto(): void
    {
        $commentDao = DAOFactory::getCommentDAO();
        $comments = $this->createCommentsForProto();
        $faker = \Faker\Factory::create();
        date_default_timezone_set('Asia/Tokyo');

        $now = new DateTime();
        $today = new DateTime('today');

        for ($i = 0; $i < count($comments); $i++) {
            $comment = $comments[$i];
            $executeAt = $faker->dateTimeBetween($now->format('Y-m-d H:i:s'), $today->format('Y-m-d 23:59:59'))->format('Y-m-d H:i:s');
            $commentDao->createForProto($i + 1, $executeAt, $comment);
        }
    }

    private function createCommentsForProto(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();
        //投稿のURLとメディアのファイル名の長さ 
        $numberOfCharacters = 18;
        $postDao = DAOFactory::getPostDAO();
        $numberOfPosts = $postDao->count();

        for ($i = 0; $i < SeedCount::USERS; $i++) {
            $userId = $i + 1;

            for ($j = 0; $j < SeedCount::COMMENTS_FOR_PROTO; $j++) {
                $comment = new Comment(
                    url: bin2hex(random_bytes($numberOfCharacters / 2)),
                    userId: $userId,
                    content: $faker->realTextBetween(10, 140, 5),
                    postId: $faker->numberBetween(1, $numberOfPosts)
                );
                $data[] = $comment;
            }
        }

        return $data;
    }

    public function deleteAllEvents(): void
    {
        $commentDao = DAOFactory::getCommentDAO();

        for ($i = 0; $i < SeedCount::USERS * SeedCount::COMMENTS_FOR_PROTO; $i++) {
            $eventName = 'random_comment_' . ($i + 1);
            $commentDao->deleteEvent($eventName);
        }
    }
}
