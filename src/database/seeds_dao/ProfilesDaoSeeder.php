<?php

namespace src\database\seeds_dao;

use finfo;
use src\database\data_access\DAOFactory;
use src\helpers\Settings;
use src\models\Profile;

class ProfilesDaoSeeder implements Seeder
{
    public function seed(): void
    {
        $profileDao = DAOFactory::getProfileDAO();
        $profiles = $this->createDummyProfiles();
        array_map(fn ($profile) => $profileDao->create($profile), $profiles);
    }

    public function createDummyProfiles(): array
    {

        $faker = \Faker\Factory::create();
        $data = [];

        $totalImages = [];
        $imageCountPerGet = 200;

        for ($i = 0; $i < ceil(SeedCount::USERS / $imageCountPerGet); $i++) {
            $images = $this->getTwoHundredImages();
            $totalImages = array_merge($totalImages, $images);
        }

        $totalImages = array_slice($totalImages, 0, SeedCount::USERS);
        $profileImagePaths = $this->saveDummyProfileImages($totalImages);

        for ($i = 0; $i < SeedCount::USERS; $i++) {
            $profileImagePath = $profileImagePaths[$i];

            $profile = new Profile(
                userId: $i + 1,
                age: $faker->numberBetween(20, 60),
                location: $faker->country(),
                description: $faker->realTextBetween(10, 140),
                profileImagePath: pathinfo($profileImagePath, PATHINFO_FILENAME),
                extension: '.' . pathinfo($profileImagePath,  PATHINFO_EXTENSION)
            );

            $data[] = $profile;
        }

        return $data;
    }

    private function getTwoHundredImages(): array
    {
        $endpoint = "https://pixabay.com/api/";
        $apiKey = Settings::env('PIXABAY_KEY');
        $faker =  \Faker\Factory::create();
        $key = "face";
        $category = $faker->randomElement(['backgrounds, fashion, nature, science, education, feelings, health, people, religion, places, animals, industry, computer, food, sports, transportation, travel, buildings, business, music']); //pixabay apiで使えるカテゴリ
        $page = $faker->numberBetween(1, 3);
        $query = "&q={$key}&category={$category}&image_type=photo&&min_width=400&min_height=400&per_page=200&pretty=true&page={$page}";
        $url = $endpoint . '?key=' . $apiKey . $query;
        $response = file_get_contents($url);
        $data = json_decode($response, true)['hits'];

        $images = [];
        $perPage = 200;
        if (!empty($data)) {
            for ($i = 0; $i < $perPage; $i++) {
                $images[] = $data[$i]['webformatURL'];
            }

            return $images;
        }
    }

    public function saveDummyProfileImages(array $images): array
    {
        $filenames = [];

        foreach ($images as $image) {
            //メディアのファイル名の長さ 
            $numberOfCharacters = 18;
            $basename = bin2hex(random_bytes($numberOfCharacters / 2));
            $extension =  '.' . pathinfo($image, PATHINFO_EXTENSION);
            $filename = $basename . $extension;
            $uploadDir = __DIR__ . "/../../../public/uploads/";
            $subdirectory = substr($filename, 0, 2) . "/";
            $mediaPath = $uploadDir .  $subdirectory . $filename;

            $imageData = file_get_contents($image);

            if ($imageData === false) {
                throw new \Exception('画像のダウンロードに失敗しました');
            } else {
                // アップロード先のディレクトリがない場合は作成
                if (!is_dir(dirname($mediaPath))) mkdir(dirname($mediaPath), 0755, true);
                file_put_contents($mediaPath, $imageData);
                $filenames[] = $mediaPath;
            }
        }


        return $filenames;
    }

    public function seedForProto(): void
    {
    }

    public function deleteAllEvents(): void
    {
    }
}
