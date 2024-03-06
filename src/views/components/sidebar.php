<!-- side bar -->
<aside class="fixed left-0 top-0 z-40 h-screen w-14 -translate-x-full transition-transform sm:translate-x-0 lg:w-40" aria-label="Sidebar">
    <div class="h-full overflow-y-auto bg-gray-50 px-2 py-4 dark:bg-gray-800">
        <a href="/" class="mb-5 flex items-center ps-2.5">
            <span class="hidden self-center whitespace-nowrap text-xl font-semibold dark:text-white lg:block">ten</span>
        </a>
        <ul class="space-y-2 font-medium">
            <li>
                <a href="/" class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                    <svg class="h-6 w-6 flex-shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.3 3.3a1 1 0 0 1 1.4 0l6 6 2 2a1 1 0 0 1-1.4 1.4l-.3-.3V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3c0 .6-.4 1-1 1H7a2 2 0 0 1-2-2v-6.6l-.3.3a1 1 0 0 1-1.4-1.4l2-2 6-6Z" clip-rule="evenodd" />
                    </svg>
                    <span class="hidden ms-3 flex-1 whitespace-nowrap lg:block">ホーム</span>
                </a>
            </li>
            <li>
                <a href="/search/user" class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                    <svg class="h-6 w-6 flex-shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" fill="none" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="3" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                    <span class="hidden ms-3 flex-1 whitespace-nowrap lg:block">検索</span>
                </a>
            </li>
            <li>
                <a href="/notifications" class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                    <div class="indicator">
                        <?php
                        $numberOfNotification = $numberOfNotification > 99 ? '99+' : $numberOfNotification;
                        ?>
                        <?php if ($numberOfNotification !== 0) : ?>
                            <span class="indicator-item badge badge-sm badge-secondary"><?= $numberOfNotification ?></span>
                        <?php endif; ?>
                        <svg class="h-6 w-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                            <path d="M17.1 12.6v-1.8A5.4 5.4 0 0 0 13 5.6V3a1 1 0 0 0-2 0v2.4a5.4 5.4 0 0 0-4 5.5v1.8c0 2.4-1.9 3-1.9 4.2 0 .6 0 1.2.5 1.2h13c.5 0 .5-.6.5-1.2 0-1.2-1.9-1.8-1.9-4.2ZM8.8 19a3.5 3.5 0 0 0 6.4 0H8.8Z" />
                        </svg>
                    </div>
                    <span class="hidden ms-3 flex-1 whitespace-nowrap lg:block">通知</span>
                </a>
            </li>
            <li>
                <a href="/messages" class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                    <svg class="h-6 w-6 flex-shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                        <path d="M17 6h-2V5h1a1 1 0 1 0 0-2h-2a1 1 0 0 0-1 1v2h-.5a6 6 0 0 1 1.5 4v4a1 1 0 1 1-2 0v-4a4 4 0 0 0-4-4h-.5C5 6 3 8 3 10.5V16c0 .6.4 1 1 1h7v3c0 .6.4 1 1 1h2c.6 0 1-.4 1-1v-3h5c.6 0 1-.4 1-1v-6a4 4 0 0 0-4-4Zm-9 8.5H7a1 1 0 1 1 0-2h1a1 1 0 1 1 0 2Z" />
                    </svg>
                    <span class="hidden ms-3 flex-1 whitespace-nowrap lg:block">メッセージ</span>

                </a>
            </li>
            <li>
                <a href="/profile<?= '?username=' . $user->getUsername(); ?>" class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                    <svg class="h-6 w-6 flex-shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                        <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd" />
                    </svg>

                    <span class="hidden ms-3 flex-1 whitespace-nowrap lg:block">プロフィール</span>
                </a>
            </li>
            <li>
                <button data-modal-target="post_modal" data-modal-show="post_modal" class="open-post-element group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                    <svg class="h-6 w-6 flex-shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 12h14m-7 7V5" />
                    </svg>
                    <span class="ms-3 flex-1 whitespace-nowrap hidden lg:block">作成</span>
                </button>
            </li>
            <li>
                <button data-modal-target="logout_modal" data-modal-show="logout_modal" class="logout-btn group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                    <svg class="h-6 w-6 flex-shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
                    </svg>
                    <span class="ms-3 flex-1 whitespace-nowrap hidden lg:block">ログアウト</span>
                </button>
            </li>
        </ul>
    </div>
</aside>