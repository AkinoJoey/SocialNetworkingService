<div class="z-30 post-dropdown">
    <button class="dropdown-btn rounded-full z-30 inline-flex items-center bg-white p-2 text-center text-sm font-medium text-gray-500 hover:bg-gray-300 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-blue-100 dark:focus:ring-gray-600" type="button">
        <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
            <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
        </svg>
        <span class="sr-only">Comment settings</span>
    </button>
    <!-- Dropdown menu -->
    <div class="dropdown-menu z-30 hidden absolute w-20 divide-y divide-gray-100 rounded bg-white shadow dark:divide-gray-600 dark:bg-gray-700">
        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownMenuIconHorizontalButton">
            <li>
                <button type="button" data-post-type="<?= $dataPostType ?>" data-post-id="<?= $dataPostId ?>" class="delete-menu-btn w-full block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 text-rose-700 font-bold">削除</button>
            </li>
        </ul>
    </div>
</div>