<!-- form -->
<div class="container mx-auto mb-14 flex items-center justify-center p-4">
    <section class="bg-gray-100 text-gray-900 w-full sm:w-4/5 md:w-3/5">
        <div class="py-8 px-4 mx-auto max-w-2xl lg:py-16">
            <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">プロフィール</h2>
            <form enctype="multipart/form-data" id="profile_form">
                <input type="hidden" name="csrf_token" value="<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>">
                <?php
                if ($profile !== null) : ?>
                    <input type="hidden" name="id" id="id" value="<?= $profile->getId() ?>">
                <?php endif; ?>
                <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                    <div class="col-span-full">
                        <label for="bio" for="profile_image_path" class="text-sm">画像</label>
                        <div class="flex items-center space-x-2">
                            <img id="user_portrait" src="<?= $profile->getProfileImagePath() === null ? '/images/user_default_portrait.png' : '/uploads/' . substr($profile->getProfileImagePath(), 0, 2) . '/' .  $profile->getProfileImagePath() . $profile->getExtension() ?>" alt="" class="w-16 h-16 rounded-full bg-gray-50 hover:cursor-pointer hover:opacity-50">
                            <input type="hidden" name="MAX_FILE_SIZE" value="41943040" />
                            <input class="hidden" id="file-input" type="file" name="media" accept="image/png, image/jpeg, image/jpg, image/webp">
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ユーザー名</label>
                        <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" value="<?= htmlspecialchars($user->getUsername()) ?>" required>
                    </div>
                    <div class="w-full">
                        <label for="age" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">年齢</label>
                        <input type="number" name="age" id="age" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" value="<?php if ($profile !== null) echo $profile->getAge() ?>" min="1" max="150">
                    </div>
                    <div class="w-full">
                        <label for="location" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">場所</label>
                        <input type="text" name="location" id="location" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" value="<?php if ($profile !== null && $profile->getLocation() !== null) echo htmlspecialchars($profile->getLocation()) ?>">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">自己紹介</label>
                        <textarea name="description" id="description" rows="8" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"><?php if ($profile !== null  && $profile->getDescription()) echo htmlspecialchars($profile->getDescription()) ?></textarea>
                    </div>


                </div>
                <div class="w-full flex justify-end">
                    <button id="profile-update" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mt-8 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        更新
                    </button>
                    <button id="loading-profile-update" type="button" disabled class="hidden text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mt-8 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        <svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor" />
                        </svg>
                        更新中...
                    </button>
                </div>

            </form>
        </div>
    </section>
</div>


<script src="/profileEdit.bundle.js"></script>