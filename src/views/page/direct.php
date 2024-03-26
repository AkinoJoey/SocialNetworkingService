<!-- chat -->
<div class="container mx-auto flex justify-center p-4">
    <div id="chat_container" class="w-full px-12 py-3 md:w-4/5 mb-20">
        <?php for ($i = 0; $i < count($messages); $i++) : ?>
            <?php $message = $messages[$i] ?>
            <?php if ($i === 0 || ($i > 0 && $message->getCreatedAt()->format('Y/m/d') !== $messages[$i - 1]->getCreatedAt()->format('Y/m/d'))) : ?>
                <div class="text-center w-full my-1 opacity-50">
                    <?= $message->getCreatedAt()->format('Y/m/d (D)') ?>
                </div>
            <?php endif; ?>
            <?php if ($message->getSenderUserId() === $user->getId()) : ?>
                <div class="chat chat-end">
                    <div class="avatar chat-image">
                        <div class="w-10 rounded-full">
                            <img alt="Tailwind CSS chat bubble component" src="<?= $user->getProfileImagePath() === null ? '/images/user_default_portrait.png' : '/uploads/' . substr($user->getProfileImagePath(), 0, 2) . '/' .  $user->getProfileImagePath() . $user->getProfileImageExtension() ?>" />
                        </div>
                    </div>
                    <div class="chat-header">
                        <?= $user->getAccountName() ?>
                    </div>
                    <div class="chat-bubble text-white bg-blue-400"><?= $message->getMessage() ?></div>
                    <div class="chat-footer opacity-50"><?= $message->getCreatedAt()->format('H:i') ?></div>
                </div>
            <?php else : ?>
                <div class="chat chat-start">
                    <div class="avatar chat-image">
                        <div class="w-10 rounded-full">
                            <img alt="Tailwind CSS chat bubble component" src="<?= $receiverUser->getProfileImagePath() === null ? '/images/user_default_portrait.png' : '/uploads/' . substr($receiverUser->getProfileImagePath(), 0, 2) . '/' .  $receiverUser->getProfileImagePath() . $receiverUser->getProfileImageExtension() ?>" />
                        </div>
                    </div>
                    <div class="chat-header">
                        <?= $receiverUser->getAccountName() ?>
                    </div>
                    <div class="chat-bubble text-black bg-gray-300 dark:bg-gray-700"><?= $message->getMessage() ?></div>
                    <div class="chat-footer opacity-50"><?= $message->getCreatedAt()->format('H:i') ?></div>
                </div>
            <?php endif; ?>
        <?php endfor; ?>
    </div>


</div>

<!-- text input -->
<div>
    <form class="fixed -translate-x-1/2 left-1/2  bottom-14 w-full sm:bottom-4 sm:pl-20 sm:pr-4 lg:pl-44">
        <div class="flex items-center rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-700">
            <textarea maxlength="140" id="message" name="message" rows="1" class="mx-4 block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" placeholder="Your message..."></textarea>
            <button id="submit_btn" type="submit" class="inline-flex cursor-pointer justify-center rounded-full p-2 text-blue-600 hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600">
                <svg class="h-5 w-5 rotate-90 rtl:-rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                </svg>
                <span class="sr-only">Send message</span>
            </button>
        </div>
    </form>
</div>

<script>
    const dmThreadId = <?= $dmThread->getId() ?>;
    const senderUserId = <?= $user->getId() ?>;
    const receiverUserId = <?= $receiverUser->getId() ?>;
    const csrfToken = "<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>";
    const receiverUserAccountName = "<?= $receiverUser->getAccountName() ?>";
    const senderUserAccountName = "<?= $user->getAccountName() ?>";
    const senderUserProfileImagePath = "<?= $user->getProfileImagePath() === null ? '/images/user_default_portrait.png' : '/uploads/' . substr($user->getProfileImagePath(), 0, 2) . '/' .  $user->getProfileImagePath() . $user->getProfileImageExtension() ?>";
    const receiverUserProfileImagePath = "<?= $receiverUser->getProfileImagePath() === null ? '/images/user_default_portrait.png' : '/uploads/' . substr($receiverUser->getProfileImagePath(), 0, 2) . '/' .  $receiverUser->getProfileImagePath() . $receiverUser->getProfileImageExtension() ?>";
</script>

<script src="/direct.bundle.js"></script>