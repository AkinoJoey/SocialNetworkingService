<!doctype html>
<html>

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script src="https://kit.fontawesome.com/51efafd327.js" crossorigin="anonymous"></script>
</head>

<!-- TODO: hover時のグレーの範囲 -->

<body>
	<div class="container mx-auto p-4">
		<?php if ($user) : ?>
			<!-- side bar -->
			<aside class="fixed left-0 top-0 z-40 h-screen w-14 -translate-x-full transition-transform sm:translate-x-0 lg:w-40" aria-label="Sidebar">
				<div class="h-full overflow-y-auto bg-gray-50 px-2 py-4 dark:bg-gray-800">
					<a href="/" class="mb-5 flex items-center ps-2.5">
						<span class="hidden self-center whitespace-nowrap text-xl font-semibold dark:text-white lg:block">ten</span>
					</a>
					<ul class="space-y-2 font-medium">
						<li>
							<a href="/" class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
								<svg class="h-6 w-6 flex-shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
									<path fill-rule="evenodd" d="M11.3 3.3a1 1 0 0 1 1.4 0l6 6 2 2a1 1 0 0 1-1.4 1.4l-.3-.3V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3c0 .6-.4 1-1 1H7a2 2 0 0 1-2-2v-6.6l-.3.3a1 1 0 0 1-1.4-1.4l2-2 6-6Z" clip-rule="evenodd" />
								</svg>
								<span class="hidden ms-3 flex-1 whitespace-nowrap lg:block">ホーム</span>
							</a>
						</li>
						<li>
							<a href="/notifications" class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
								<svg class="h-6 w-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
									<path d="M17.1 12.6v-1.8A5.4 5.4 0 0 0 13 5.6V3a1 1 0 0 0-2 0v2.4a5.4 5.4 0 0 0-4 5.5v1.8c0 2.4-1.9 3-1.9 4.2 0 .6 0 1.2.5 1.2h13c.5 0 .5-.6.5-1.2 0-1.2-1.9-1.8-1.9-4.2ZM8.8 19a3.5 3.5 0 0 0 6.4 0H8.8Z" />
								</svg>

								<span class="hidden ms-3 flex-1 whitespace-nowrap lg:block">通知</span>
							</a>
						</li>
						<li>
							<a href="/messages" class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
								<svg class="h-6 w-6 flex-shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
									<path d="M17 6h-2V5h1a1 1 0 1 0 0-2h-2a1 1 0 0 0-1 1v2h-.5a6 6 0 0 1 1.5 4v4a1 1 0 1 1-2 0v-4a4 4 0 0 0-4-4h-.5C5 6 3 8 3 10.5V16c0 .6.4 1 1 1h7v3c0 .6.4 1 1 1h2c.6 0 1-.4 1-1v-3h5c.6 0 1-.4 1-1v-6a4 4 0 0 0-4-4Zm-9 8.5H7a1 1 0 1 1 0-2h1a1 1 0 1 1 0 2Z" />
								</svg>
								<span class="hidden ms-3 flex-1 whitespace-nowrap lg:block">メッセージ</span>

							</a>
						</li>
						<li>
							<a href="/profile<?= '?username=' . $user->getUsername(); ?>" class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
								<svg class="h-6 w-6 flex-shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
									<path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd" />
								</svg>

								<span class="hidden ms-3 flex-1 whitespace-nowrap lg:block">プロフィール</span>
							</a>
						</li>
						<li>
							<!-- TODO: ログアウトモーダルの実装 -->
							<a href="/logout" class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
								<svg class="h-6 w-6 flex-shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none">
									<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
								</svg>
								<span class="ms-3 flex-1 whitespace-nowrap hidden lg:block">ログアウト</span>
							</a>
						</li>
						<li>
							<button data-modal-target="post_modal" data-modal-show="post_modal" class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
								<svg class="h-6 w-6 flex-shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
									<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 12h14m-7 7V5" />
								</svg>
								<span class="ms-3 flex-1 whitespace-nowrap hidden lg:block">作成</span>
							</button>
						</li>
					</ul>
				</div>
			</aside>

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
					<form action="/form/new" method="POST">
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
		<?php endif; ?>


	</div>