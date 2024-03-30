<?php

namespace src\database\seeds_dao;

use src\database\data_access\DAOFactory;
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

        for ($i = 0; $i < SeedCount::USERS; $i++) {
            // $profileImagePath = $this->saveDummyProfileImage();

            $profile = new Profile(
                userId: $i + 1,
                age: $faker->numberBetween(20, 60),
                location: $faker->country(),
                description: $faker->realTextBetween(10, 140),
                // profileImagePath: pathinfo($profileImagePath, PATHINFO_FILENAME),
                // extension: '.' . pathinfo($profileImagePath,  PATHINFO_EXTENSION)
            );

            $data[] = $profile;
        }

        return $data;
    }

    public function getAvatarFileName(): string
    {
        $filename = $this->saveDummyProfileImage();
        return $filename;
    }

    /**
     * @return string $filename
     */
    public function saveDummyProfileImage(): string
    {
        $unsplashUrl = "https://source.unsplash.com/400x400/?portrait";

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
            return $filename;
        }
    }

    public function seedForProto(): void
    {
    }

    public function deleteAllEvents(): void
    {
    }
}
