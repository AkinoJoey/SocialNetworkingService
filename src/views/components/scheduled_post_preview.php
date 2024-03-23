<!-- preview modal -->
<div id="preview_modal" class="fixed left-0 right-0 top-0 z-50 hidden h-screen w-full items-center justify-center">
    <div class="w-full max-h-96 sm:max-h-[570px] overflow-auto rounded-xl bg-white sm:w-2/3 lg:w-2/5">
        <div class="flex items-center justify-between border-b px-5 py-2 text-blue-400">
            <button id="preview_modal_hide_btn" class="rounded-full px-2 hover:bg-blue-100">
                <i class="fas fa-times text-xl"></i>
            </button>

            <p class="text-gray-500 text-sm"><span id="scheduled_at_container"></span>に投稿予定</p>
        </div>
        <form enctype="multipart/form-data" id="create-post-form">
            <div class="flex p-4">
                <div>
                    <img class="w-14 rounded-full" src="<?= $user->getProfileImagePath() === null ? '/images/user_default_portrait.png' : '/uploads/' . substr($user->getProfileImagePath(), 0, 2) . '/' .  $user->getProfileImagePath() . $user->getProfileImageExtension() ?>" />
                </div>

                <div class="ml-3 flex w-full flex-col">
                    <div id="preview_content" class="h-32 w-full resize-none rounded-xl text-xl border-none focus:ring-0">

                    </div>
                    <div class="w-full flex justify-center rounded-full relative" id="preview_media_container">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>