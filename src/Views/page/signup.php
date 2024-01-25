<div class="container mx-auto flex h-screen items-center justify-center px-4">
	<div class="flex max-w-md flex-col rounded-md bg-gray-50 p-6 text-gray-800 sm:p-10">
		<div class="mb-8 text-center">
			<h1 class="my-3 text-4xl font-bold">アカウントを作成</h1>
		</div>
		<form novalidate="" action="" class="space-y-12" data-bitwarden-watching="1">
			<div class="space-y-4">
				<div>
					<label for="email" class="mb-2 block text-sm">Eメール</label>
					<input type="email" name="email" id="email" placeholder="taro-yamada@example.com" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800" />
				</div>
				<div>
					<div class="mb-2 flex justify-between">
						<label for="password" class="text-sm">パスワード</label>
					</div>
					<input type="password" name="password" id="password" placeholder="*********" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800" />
				</div>
				<div>
					<div class="mb-2 flex justify-between">
						<label for="account-name" class="text-sm">アカウント名</label>
					</div>
					<input type="text" name="account-name" id="account-name" placeholder="taro-yamada" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800" />
				</div>
				<div>
					<div class="mb-2 flex justify-between">
						<label for="display-name" class="text-sm">表示名</label>
					</div>
					<input type="text" name="display-name" id="display-name" placeholder="山田 太郎" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800" />
				</div>
			</div>
			<div class="space-y-2">
				<div class="flex justify-center">
					<button type="button" class="mb-2 me-2 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
						登録
					</button>
				</div>
			</div>
		</form>
	</div>
</div>