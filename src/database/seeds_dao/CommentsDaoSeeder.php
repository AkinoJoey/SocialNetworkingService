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
        $comments = $this->createDummyComments();
        array_map(fn($comment)=> $commentDao->create($comment), $comments);
    }

    public function createDummyComments(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();
        $numberOfDummyPosts = 300;
        //投稿のURLとメディアのファイル名の長さ 
        $numberOfCharacters = 18;

        for ($i = 0; $i < $numberOfDummyPosts; $i++) {
            $comment = new Comment(
                url: bin2hex(random_bytes($numberOfCharacters / 2)),
                userId: $faker->numberBetween(1, 20),
                content: $faker->realTextBetween(10, 140, 5),
                postId: $faker->numberBetween(1, 100),
                timeStamp: new DataTimeStamp($faker->dateTimeBetween('-1 years')->format('Y-m-d H:i:s'), (new DateTime())->format("Y-m-d H:i:s")),
            );
            
            $data[] = $comment;
        }

        return $data;
    }
}
