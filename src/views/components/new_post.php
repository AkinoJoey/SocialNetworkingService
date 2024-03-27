<!-- post modal -->
<div id="post_modal" class="fixed left-0 right-0 top-0 z-50 hidden h-screen w-full items-center justify-center">
    <div class="w-full max-h-96 sm:max-h-[570px] overflow-auto rounded-xl bg-white sm:w-2/3 lg:w-2/5">
        <div class="flex items-center justify-between border-b px-5 py-3 text-blue-400">
            <button data-modal-hide="post_modal" class="rounded-full px-3 py-1 hover:bg-blue-100">
                <i class="fas fa-times text-xl"></i>
            </button>

            <a href="/scheduled_posts" class="inline cursor-pointer rounded-full px-4 py-3 font-bold hover:bg-blue-100">
                予定投稿一覧
            </a>
        </div>
        <form enctype="multipart/form-data" id="create-post-form">
            <input type="hidden" name="csrf_token" value="<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>">
            <div class="flex p-4">
                <div class="min-w-12">
                    <img class="w-12 h-12 rounded-full object-cover" src="<?= $user->getProfileImagePath() === null ? '/images/user_default_portrait.png' : '/uploads/' . substr($user->getProfileImagePath(), 0, 2) . '/' .  $user->getProfileImagePath() . $user->getProfileImageExtension() ?>" />
                </div>

                <div class="ml-3 flex w-full flex-col">
                    <textarea id="content" name="content" placeholder="今何してる?" class="h-32 w-full resize-none rounded-xl text-xl border-none focus:ring-0" maxlength="280"></textarea>
                    <input type="hidden" name="MAX_FILE_SIZE" value="41943040" />
                    <input class="hidden" id="file-input" type="file" name="media" accept="image/png, image/gif, image/jpeg, image/jpg, image/webp, video/mp4, video/quicktime">
                    <div class="w-full flex justify-center rounded-full relative" id="previewContainer">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between border-t px-4 py-6 text-blue-400">
                <div class="flex text-2xl">
                    <div id="file-input-icon" class="flex cursor-pointer items-center justify-center rounded-full p-3 hover:bg-blue-100">
                        <i class="fas fa-image"></i>
                    </div>
                    <button type="button" data-modal-target="schedule_modal" data-modal-toggle="schedule_modal" id="calender-icon" class="flex cursor-pointer items-center justify-center rounded-full p-3 hover:bg-blue-100">
                        <i class="fas fa-calendar-alt"></i>
                    </button>

                </div>
                <div class="flex items-center justify-between">
                    <div id="scheduledAt_container" class="flex items-center justify-center rounded-full p-3 text-sm">
                    </div>
                    <button id="post-submit" type="submit" class="inline cursor-pointer rounded-full bg-blue-500 px-4 py-3 font-bold text-white hover:opacity-75 btn btn-disabled">
                        投稿
                    </button>
                    <button id="post-loading-btn" type="button" class="hidden cursor-pointer rounded-full bg-blue-500 px-4 py-3 font-bold text-white hover:opacity-75 btn btn-disabled">
                        <svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor" />
                        </svg>
                        送信中...
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>