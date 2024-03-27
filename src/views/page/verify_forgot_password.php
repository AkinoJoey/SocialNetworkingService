<div class="container mx-auto h-screen flex items-center justify-center px-4 mb-16 mt-4 sm:my-0">
    <div class="flex max-w-md flex-col rounded-md bg-gray-50 p-6 text-gray-800 sm:p-10">
        <div class="text-center">
            <h1 class="my-3 text-4xl font-bold">パスワードをリセット</h1>
        </div>
        <form id="password_form" class="space-y-12" data-bitwarden-watching="1">
            <input type="hidden" name="csrf_token" value="<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>">
            <input type="hidden" name="user_id" value="<?= $userId ?>">
            <div class="space-y-4">
                <div>
                    <div class="mb-2 flex justify-between">
                        <label for="password" class="text-sm">新しいパスワード（8文字以上30文字以内、半角の大文字、小文字、数字、記号を1つ以上含めてください）</label>
                    </div>
                    <input type="password" name="password" id="password" autocomplete="new-password" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800" required />
                </div>
                <div>
                    <div class="mb-2 flex justify-between">
                        <label for="confirm_password" class="text-sm">パスワードを再入力</label>
                    </div>
                    <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800" required />
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-center">
                    <button id="submit_btn" type="submit" class="mb-2 me-2 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        送信
                    </button>
                    <?php include(__DIR__ . '/../components/sending_spinner_button.php') ?>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="/verifyForgotPassword.bundle.js"></script>