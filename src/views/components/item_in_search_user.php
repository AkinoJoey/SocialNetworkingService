<div class="divide-y divide-gray-100 dark:divide-gray-700">
    <a href="/profile?username=<?= htmlspecialchars($user->getUsername()) ?>" class="notification flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 hover:cursor-pointer">
        <div class="flex w-full items-center">
            <div class="mr-2">
                <img class="rounded-full w-16 h-16 object-cover" src="<?= $user->getProfileImagePath() === null ? '/images/user_default_portrait.png' : '/uploads/' . substr($user->getProfileImagePath(), 0, 2) . '/' .  $user->getProfileImagePath() . $user->getProfileImageExtension() ?>" alt="user avatar" />
            </div>
            <div class="w-3/4">
                <div class="flex h-full flex-col justify-center">
                    <div class="mb-1.5 text-sm text-gray-950 dark:text-gray-400">
                        <span class="font-semibold text-gray-900 dark:text-white "><?= htmlspecialchars($user->getAccountName()) ?></span>
                    </div>
                    <div class="text-xs text-gray-500">
                        <?= '@' . htmlspecialchars($user->getUsername()) ?>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>