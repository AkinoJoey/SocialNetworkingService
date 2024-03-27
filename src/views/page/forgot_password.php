<div class="container mx-auto flex items-center justify-center px-4 mt-24">
    <div class="flex max-w-md flex-col rounded-md bg-gray-50 p-6 text-gray-800">
        <div class="text-center">
            <h1 class="my-3 text-4xl font-bold">パスワードを忘れましたか？</h1>
        </div>
        <form id="forgot_password_form" class="space-y-12" data-bitwarden-watching="1">
            <input type="hidden" name="csrf_token" value="<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>">
            <div class="space-y-4">
                <div>
                    <p class="mb-3">アカウント作成に使用したメールアドレスを入力してください</p>
                    <input type="email" name="email" id="email" autocomplete="email" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800" required />
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-center">
                    <button type="submit" id="submit_btn" class="mb-2 me-2 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        送信
                    </button>
                    <?php include(__DIR__ . '/../components/sending_spinner_button.php') ?>
                </div>

            </div>
        </form>
    </div>
</div>

<script src="/forgotPassword.bundle.js"></script>