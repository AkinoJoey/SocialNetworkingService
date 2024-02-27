<div class="container mx-auto mb-14 flex items-center justify-center p-4">
    <div class="w-full max-w-2xl divide-y divide-gray-100 rounded-lg bg-white shadow dark:divide-gray-700 dark:bg-gray-800 sm:m-12 sm:w-8/12 lg:mx-40">
        <!-- search form -->
        <form id="search-form" class="w-full">
            <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="search" name="keyword" id="keyword" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="ユーザーを検索" />
                <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
            </div>
        </form>

        <?php foreach ($users as $user) : ?>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <a href="/profile?username=<?= $user->getUsername() ?>" class="notification flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 hover:cursor-pointer">
                    <div class="flex w-full items-center">
                        <div class="mr-2">
                            <img class="rounded-full w-16" src="https://source.unsplash.com/100x100/?portrait" alt="Jese image" />
                        </div>
                        <div class="w-3/4">
                            <div class="flex h-full flex-col justify-center">
                                <div class="mb-1.5 text-sm text-gray-950 dark:text-gray-400">
                                    <span class="font-semibold text-gray-900 dark:text-white "><?= $user->getAccountName() ?></span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= '@' . $user->getUsername() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let searchForm = document.getElementById('search-form');
        const userList = document.getElementById('user-list');
        const inputKeyword = document.getElementById('keyword');
        inputKeyword.focus();

        inputKeyword.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                search();
            }
        })

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            search();
        })

        function search() {
            const formData = new FormData(searchForm);
            window.location.href = `/search/user?keyword=${formData.get('keyword')}`;
        }
    })
</script>