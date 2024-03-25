<div class="container mx-auto flex items-center justify-center mt-24">
	<div class="flex flex-col w-96 rounded-md bg-gray-50 p-6 text-gray-800">
		<div class="text-center">
			<h1 class="my-3 text-4xl font-bold">アカウントを作成</h1>
		</div>
		<form id="signup_form" class="space-y-12">
			<input type="hidden" name="csrf_token" value="<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>">
			<div class="space-y-4">
				<div>
					<div class="mb-2 flex justify-between">
						<label for="account_name" class="text-sm">名前</label>
					</div>
					<input type="text" name="account_name" id="account_name" autocomplete="name" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800" required />
				</div>
				<div>
					<label for="email" class="mb-2 block text-sm">Eメール</label>
					<input type="email" name="email" id="email" autocomplete="email" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800" required />
				</div>
				<div>
					<div class="mb-2 flex justify-between">
						<label for="password" class="text-sm">パスワード<span class="text-sm">（8文字以上30文字以内、半角の大文字、小文字、数字、記号を1つ以上含めてください）</span></label>
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
					<button id="submit_btn" type="submit" class="mb-2 me-2 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800  dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
						登録
					</button>
					<button id="loading_btn" disabled type="button" class="hidden mb-2 me-2 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
						<svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB" />
							<path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor" />
						</svg>
						Loading...
				</div>
			</div>
		</form>
	</div>
</div>
</div>

<!-- TODO: submit後にloadingを行う -->

<script src="/signup.bundle.js"></script>