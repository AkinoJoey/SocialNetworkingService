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
					<?php include(__DIR__ . '/../components/sending_spinner_button.php') ?>
				</div>
			</div>
		</form>
	</div>
</div>
</div>

<!-- TODO: submit後にloadingを行う -->

<script src="/signup.bundle.js"></script>