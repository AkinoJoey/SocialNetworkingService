		<!-- post modal -->
		<div id="post_modal" class="fixed left-0 right-0 top-0 z-50 hidden h-screen w-full items-center justify-center">
			<div class="w-full rounded-xl bg-white sm:w-2/3 lg:w-1/2">
				<div class="flex items-center justify-between border-b px-5 py-3 text-blue-400">
					<button data-modal-hide="post_modal" class="rounded-full px-4 py-3 hover:bg-blue-100">
						<i class="fas fa-times text-xl"></i>
					</button>

					<p class="inline cursor-pointer rounded-full px-4 py-3 font-bold hover:bg-blue-100">
						未送信ポスト
					</p>
				</div>
				<form action="/form/post" method="POST">
					<input type="hidden" name="csrf_token" value="<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>">
					<div class="flex p-4">
						<div>
							<img class="w-14 rounded-full" src="https://source.unsplash.com/100x100/?portrait" />
						</div>

						<div class="ml-3 flex w-full flex-col">
							<textarea id="content" name="content" placeholder="What's happening?" class="h-32 w-full resize-none rounded-xl border-gray-200 text-xl outline-none"></textarea>
						</div>
					</div>

					<div class="flex items-center justify-between border-t px-4 py-6 text-blue-400">
						<div class="flex pl-12 text-2xl">
							<div class="flex cursor-pointer items-center justify-center rounded-full p-3 hover:bg-blue-100">
								<i class="fas fa-image"></i>
							</div>

							<div class="flex cursor-pointer items-center justify-center rounded-full p-3 hover:bg-blue-100">
								<i class="fas fa-poll-h"></i>
							</div>

							<div class="flex cursor-pointer items-center justify-center rounded-full p-3 hover:bg-blue-100">
								<i class="fas fa-smile"></i>
							</div>

							<div class="flex cursor-pointer items-center justify-center rounded-full p-3 hover:bg-blue-100">
								<i class="fas fa-calendar-alt"></i>
							</div>
						</div>

						<div>
							<button type="submit" class="inline cursor-pointer rounded-full bg-blue-500 px-4 py-3 font-bold text-white">
								Tweet
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- bottom nav -->
		<div class="fixed bottom-0 left-1/2 z-50 w-full -translate-x-1/2 border-t border-gray-200 bg-white dark:border-gray-600 dark:bg-gray-700 sm:hidden">
			<div class="mx-auto grid h-full max-w-lg grid-cols-5">
				<button data-tooltip-target="tooltip-home" type="button" class="group inline-flex flex-col items-center justify-center p-4 hover:bg-gray-50 dark:hover:bg-gray-800"><a href="/">
						<svg class="mb-1 h-5 w-5 text-gray-500 group-hover:text-blue-600 dark:text-gray-400 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
							<path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
						</svg>
						<span class="sr-only">ホーム</span>
					</a>

				</button>
				<div id="tooltip-home" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
					ホーム
					<div class="tooltip-arrow" data-popper-arrow></div>
				</div>
				<button data-tooltip-target="tooltip-bookmark" type="button" class="group inline-flex flex-col items-center justify-center p-4 hover:bg-gray-50 dark:hover:bg-gray-800">
					<svg class="mb-1 h-5 w-5 text-gray-500 group-hover:text-blue-600 dark:text-gray-400 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 20">
						<path d="M13 20a1 1 0 0 1-.64-.231L7 15.3l-5.36 4.469A1 1 0 0 1 0 19V2a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v17a1 1 0 0 1-1 1Z" />
					</svg>
					<span class="sr-only">Bookmark</span>
				</button>
				<div id="tooltip-bookmark" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
					Bookmark
					<div class="tooltip-arrow" data-popper-arrow></div>
				</div>
				<button data-modal-target="post_modal" data-modal-show="post_modal" data-tooltip-target="tooltip-post" type="button" class="group inline-flex flex-col items-center justify-center p-4 hover:bg-gray-50 dark:hover:bg-gray-800">
					<svg class="mb-1 h-5 w-5 text-gray-500 group-hover:text-blue-600 dark:text-gray-400 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
						<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
					</svg>
					<span class="sr-only">New</span>
				</button>
				<div id="tooltip-post" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
					New post
					<div class="tooltip-arrow" data-popper-arrow></div>
				</div>
				<button data-tooltip-target="tooltip-search" type="button" class="group inline-flex flex-col items-center justify-center p-4 hover:bg-gray-50 dark:hover:bg-gray-800">
					<svg class="mb-1 h-5 w-5 text-gray-500 group-hover:text-blue-600 dark:text-gray-400 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
						<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
					</svg>
					<span class="sr-only">Search</span>
				</button>
				<div id="tooltip-search" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
					Search
					<div class="tooltip-arrow" data-popper-arrow></div>
				</div>
				<button data-tooltip-target="tooltip-settings" type="button" class="group inline-flex flex-col items-center justify-center p-4 hover:bg-gray-50 dark:hover:bg-gray-800">
					<svg class="mb-1 h-5 w-5 text-gray-500 group-hover:text-blue-600 dark:text-gray-400 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
						<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12.25V1m0 11.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M4 19v-2.25m6-13.5V1m0 2.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M10 19V7.75m6 4.5V1m0 11.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5ZM16 19v-2" />
					</svg>



					<span class="sr-only">プロフィール</span>
				</button>
				<div id="tooltip-settings" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
					プロフィール
					<div class="tooltip-arrow" data-popper-arrow></div>
				</div>
			</div>
		</div>
		</div>
		<script src="/bundle.js"></script>
		</body>

		</html>