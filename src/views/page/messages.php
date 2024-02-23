<!-- message list -->
<div class="container mx-auto mb-14 flex items-center justify-center p-4">
    <div class="w-full max-w-2xl divide-y divide-gray-100 rounded-lg bg-white shadow dark:divide-gray-700 dark:bg-gray-800 sm:m-12 sm:w-8/12 lg:mx-40">
        <div class="block rounded-t-lg bg-gray-50 px-4 py-2 text-center font-medium text-gray-700 dark:bg-gray-800 dark:text-white">
            メッセージ
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($threads as $thread) : ?>
                <a href="direct?url=<?= $thread->getUrl() ?>" data-thread-id="<?= $thread->getId() ?>" class="notification flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 hover:cursor-pointer">
                    <div class="flex w-full items-center">
                        <div class="mr-2">
                            <img class="rounded-full" src="https://source.unsplash.com/100x100/?portrait" alt="Jese image" />
                        </div>
                        <div class="w-3/4">
                            <div class="flex h-full flex-col justify-center">
                                <div class="mb-1.5 text-sm text-gray-950 dark:text-gray-400">
                                    <span class="font-semibold text-gray-900 dark:text-white"><?= $sourceUser->getAccountName() ?></span>
                                    さんとのメッセージ
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<script>
    const csrfToken = "<?= src\helpers\CrossSiteForgeryProtection::getToken(); ?>"
</script>
<script src="/notifications.bundle.js"></script>