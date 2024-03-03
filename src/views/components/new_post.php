<!-- post modal -->
<div id="post_modal" class="fixed left-0 right-0 top-0 z-50 hidden h-screen w-full items-center justify-center">
    <div class="w-full max-h-96 sm:max-h-[570px] overflow-auto rounded-xl bg-white sm:w-2/3 lg:w-2/5">
        <div class="flex items-center justify-between border-b px-5 py-3 text-blue-400">
            <button data-modal-hide="post_modal" class="rounded-full px-3 py-1 hover:bg-blue-100">
                <i class="fas fa-times text-xl"></i>
            </button>

            <p class="inline cursor-pointer rounded-full px-4 py-3 font-bold hover:bg-blue-100">
                未送信ポスト
            </p>
        </div>
        <form enctype="multipart/form-data" id="create-post-form">
            <input type="hidden" name="csrf_token" value="<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>">
            <div class="flex p-4">
                <div>
                    <img class="w-14 rounded-full" src="https://source.unsplash.com/100x100/?portrait" />
                </div>

                <div class="ml-3 flex w-full flex-col">
                    <textarea id="content" name="content" placeholder="今何してる?" class="h-32 w-full resize-none rounded-xl text-xl border-none focus:ring-0" maxlength="280"></textarea>
                    <input type="hidden" name="MAX_FILE_SIZE" value="41943040" />
                    <input class="hidden" id="file-input" type="file" name="media" accept="image/png, image/gif, image/jpeg, image/jpg, image/webp, video/mp4, video/mov">
                    <div class="w-full flex justify-center rounded-full relative" id="previewContainer">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between border-t px-4 py-6 text-blue-400">
                <div class="flex pl-12 text-2xl">
                    <div id="file-input-icon" class="flex cursor-pointer items-center justify-center rounded-full p-3 hover:bg-blue-100">
                        <i class="fas fa-image"></i>
                    </div>
                    <div id="calender" class="flex cursor-pointer items-center justify-center rounded-full p-3 hover:bg-blue-100">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div>
                    <button id="post-submit" type="submit" class="inline cursor-pointer rounded-full bg-blue-500 px-4 py-3 font-bold text-white hover:opacity-75 btn btn-disabled">
                        投稿
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>