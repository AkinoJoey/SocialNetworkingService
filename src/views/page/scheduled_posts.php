<?php

use Carbon\Carbon;
?>
<!-- message list -->
<div class="container mx-auto mb-14 flex items-center justify-center p-4">
    <div class="w-full max-w-2xl divide-y divide-gray-100 rounded-lg bg-white shadow dark:divide-gray-700 dark:bg-gray-800 sm:m-12 sm:w-8/12 lg:mx-40">
        <div class="block rounded-t-lg bg-gray-50 px-4 py-2 text-center font-medium text-gray-700 dark:bg-gray-800 dark:text-white">
            予約投稿
        </div>
        <?php foreach ($scheduledPosts as $post) : ?>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="flex w-full items-center hover:cursor-pointer scheduled-post">
                    <div class="w-3/4 ml-4 ">
                        <div class="flex h-full flex-col justify-center">
                            <div class="mb-1.5 text-sm">
                                <p class="scheduled-post-content text-sm mt-1 text-gray-600 dark:text-gray-400"><?= $post->getContent() ?></p>
                            </div>
                            <div class="text-xs text-blue-600 dark:text-blue-500">
                                <span class="scheduled-at"><?= Carbon::parse($post->getScheduledAt())->format('Y-m-d H:i') ?></span>に投稿予定
                            </div>
                        </div>
                    </div>
                    <div class="w-1/4 flex h-full justify-center items-center">
                        <button type="button" class="delete-schedule-post mt-2 focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 z-10">削除</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="/scheduledPosts.bundle.js"></script>