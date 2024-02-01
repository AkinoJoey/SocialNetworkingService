<!-- form -->
<div class="flex justify-center items-center p-6 container mb-6">
    <section class="bg-gray-100 text-gray-900 w-full sm:w-4/5 md:w-3/5">
        <div class="py-8 px-4 mx-auto max-w-2xl lg:py-16">
            <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">プロフィール</h2>
            <form action="/form/profile/edit" method="POST">
                <input type="hidden" name="csrf_token" value="<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>">
                <?php if ($profile !== null) : ?>
                    <input type="hidden" name="id" id="id" value="<?= $profile->getId() ?>">
                <?php endif; ?>
                <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                    <div class="sm:col-span-2">
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ユーザー名</label>
                        <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" value="<?= $user->getUsername() ?>" required>
                    </div>
                    <div class="w-full">
                        <label for="age" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">年齢</label>
                        <input type="text" name="age" id="age" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" value="<?php if ($profile !== null) echo $profile->getAge() ?>">
                    </div>
                    <div class="w-full">
                        <label for="location" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">場所</label>
                        <input type="text" name="location" id="location" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" value="<?php if ($profile !== null) echo $profile->getLocation() ?>">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">自己紹介</label>
                        <textarea name="description" id="description" rows="8" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"><?php if ($profile !== null) echo $profile->getDescription() ?></textarea>
                    </div>

                    <div class="col-span-full">
                        <label for="bio" for="profile_image_path" class="text-sm">プロフィール画像</label>
                        <div class="flex items-center space-x-2">
                            <img src="https://source.unsplash.com/30x30/?random" alt="" class="w-16 h-16 rounded-full bg-gray-50">
                            <button type="button" class="px-4 py-2 border rounded-md border-gray-800">Change</button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="inline-flex bg-blue-700 items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800">
                    <?php if ($profile !== null) : ?>
                        更新
                    <?php else : ?>
                        作成
                    <?php endif; ?>
                </button>
            </form>
        </div>
    </section>
</div>