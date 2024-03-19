<div class="container mx-auto flex items-center justify-center px-4 mb-16 mt-4 sm:my-0">
    <div class="flex max-w-md flex-col rounded-md bg-gray-50 p-6 text-gray-800 sm:p-10">
        <div class="text-center">
            <h1 class="my-3 text-4xl font-bold">パスワードをリセット</h1>
        </div>
        <form action="form/login" method="POST" class="space-y-12" data-bitwarden-watching="1">
            <input type="hidden" name="csrf_token" value="<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>">
            <div class="space-y-4">
                <div>
                    <label for="email" class="mb-2 block text-sm">Eメール</label>
                    <input type="email" name="email" id="email" autocomplete="email" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800" />
                </div>
                <div>
                    <div class="mb-2 flex justify-between">
                        <label for="password" class="text-sm">パスワード</label>
                        <a href="/forgot_password" rel="noopener noreferrer" href="#" class="text-xs text-gray-600 hover:underline">パスワードを忘れた場合</a>
                    </div>
                    <input type="password" name="password" id="password" autocomplete="current-password" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800" />
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-center">
                    <button type="submit" class="mb-2 me-2 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        送信
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>