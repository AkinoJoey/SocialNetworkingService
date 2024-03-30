<?php

namespace src\database\seeds_dao;

use DateTime;
use src\database\data_access\DAOFactory;
use src\helpers\MediaHelper;
use src\models\DataTimeStamp;
use src\models\Post;

class PostsDaoSeeder implements Seeder
{
    public function seed(): void
    {
        $postDao = DAOFactory::getPostDAO();
        $posts = $this->createDummyPosts();
        array_map(fn ($post) => $postDao->createForDummy($post), $posts);
    }


    private function createDummyPosts(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();
        //投稿のURLとメディアのファイル名の長さ 
        $numberOfCharacters = 18;

        for ($i = 0; $i < SeedCount::POSTS; $i++) {
            // 20分の1の確率で画像を生成する
            if (rand(1, 20) === 1) {
                $mediaPath = $this->getDummyPostMediaFileName();
            } else {
                $mediaPath = null;
            }

            $post = new Post(
                url: bin2hex(random_bytes($numberOfCharacters / 2)),
                userId: $faker->numberBetween(1, SeedCount::USERS),
                content: $faker->realTextBetween(10, 140, 5),
                timeStamp: new DataTimeStamp($faker->dateTimeBetween('-1 years')->format('Y-m-d H:i:s'), (new DateTime())->format("Y-m-d H:i:s")),
                mediaPath: isset($mediaPath) ? pathinfo($mediaPath, PATHINFO_FILENAME) : null,
                extension: isset($mediaPath) ? '.' . pathinfo($mediaPath, PATHINFO_EXTENSION) : null
            );
            $data[] = $post;
        }

        return $data;
    }

    private function getDummyPostMediaFileName(): string
    {
        $filename = $this->saveDummyImage();
        return $filename;
    }

    /**
     * @return string $filename
     */
    private function saveDummyImage(): string
    {
        $unsplashUrl = "https://source.unsplash.com/700x700/?random";

        //メディアのファイル名の長さ 
        $numberOfCharacters = 18;
        $basename = bin2hex(random_bytes($numberOfCharacters / 2));
        $extension =  \Faker\Factory::create()->randomElement(['.jpg', '.png', '.jpeg', '.webp']);
        $filename = $basename . $extension;
        $uploadDir = __DIR__ . "/../../../public/uploads/";
        $subdirectory = substr($filename, 0, 2) . "/";
        $mediaPath = $uploadDir .  $subdirectory . $filename;

        $imageData = file_get_contents($unsplashUrl);

        if ($imageData === false) {
            throw new \Exception('画像のダウンロードに失敗しました');
        } else {
            // アップロード先のディレクトリがない場合は作成
            if (!is_dir(dirname($mediaPath))) mkdir(dirname($mediaPath), 0755, true);
            file_put_contents($mediaPath, $imageData);

            $thumbnailPath = $uploadDir .  $subdirectory . explode(".", $filename)[0] . "_thumb" . $extension;

            $success = MediaHelper::createThumbnail(
                $mediaPath,
                $thumbnailPath,
                '720x720'
            );

            if (!$success) throw new \Exception('サムネイルの作成に失敗しました');

            return $filename;
        }
    }

    public function seedForProto(): void
    {
        $postDao = DAOFactory::getPostDAO();
        $posts = $this->createPostsForProto();
        $faker = \Faker\Factory::create();
        date_default_timezone_set('Asia/Tokyo');

        $now = new DateTime();
        $today = new DateTime('today');

        for ($i = 0; $i < count($posts); $i++) {
            $post = $posts[$i];
            $executeAt = $faker->dateTimeBetween($now->format('Y-m-d H:i:s'), $today->format('Y-m-d 23:59:59'))->format('Y-m-d H:i:s');
            $postDao->createForProto($i + 1, $executeAt, $post);
        }
    }

    private function createPostsForProto(): array
    {
        $data = [];
        $faker = \Faker\Factory::create();
        //投稿のURLの長さ 
        $numberOfCharacters = 18;

        for ($i = 0; $i < SeedCount::USERS; $i++) {
            $userId = $i + 1;

            for ($j = 0; $j < SeedCount::POSTS_FOR_PROTO; $j++) {
                $post = new Post(
                    url: bin2hex(random_bytes($numberOfCharacters / 2)),
                    userId: $userId,
                    content: $faker->realTextBetween(10, 140, 5),
                );
                $data[] = $post;
            }
        }

        return $data;
    }


    public function deleteAllEvents(): void
    {
        $postDao = DAOFactory::getPostDAO();

        for ($i = 0; $i < SeedCount::USERS * SeedCount::POST_LIKES_FOR_PROTO; $i++) {
            $eventName = 'random_post_' . ($i + 1);
            $postDao->deleteEvent($eventName);
        }
    }
}
