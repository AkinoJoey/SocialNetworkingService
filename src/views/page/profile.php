<!-- profile -->
<div class="container flex justify-center p-4">
	<div class="w-full rounded-lg p-6 shadow-md dark:bg-gray-900 dark:text-gray-100 sm:m-12 lg:mx-40 max-w-2xl">
		<div class="flex flex-col">
			<div class="w-full flex flex-row">
				<div class="w-1/3">
					<img src="https://source.unsplash.com/75x75/?portrait" alt="" class="flex-shrink-0 self-center rounded-full border dark:border-gray-700 dark:bg-gray-500 lg:justify-self-start" />
				</div>
				<!-- TODO: 10Kみたいな表示にする -->
				<div class="w-2/3 flex justify-around">
					<div class="flex flex-col justify-around font-semibold">
						<p>フォロー中</p>
						<p class="text-center"><?= $followingCount ?></p>
					</div>
					<div class="flex flex-col justify-around font-semibold">
						<p>フォロワー</p>
						<p id="followerCount" class="text-center"><?= $followerCount ?></p>
					</div>
				</div>
			</div>

			<div class="flex flex-col">
				<h4 class="text-lg font-semibold">
					<?= $user->getAccountName() ?>
				</h4>
				<h4 class="text-sm text-gray-500 dark:text-gray-400">
					<?= "@" . $user->getUsername() ?>
				</h4>
				<p class="dark:text-gray-400">
					<?= $profile->getDescription() ?>
				</p>
			</div>
		</div>
		<?php if ($user->getId() === $authenticatedUser->getId()) : ?>
			<div class="align-center flex justify-center space-x-4 pt-4">
				<button class="btn btn-active">
					<a href="/profile/edit">プロフィールを編集</a>
				</button>
				<button class="btn btn-active">
					<a href="/logout">ログアウト</a>
				</button>
			</div>
		<?php else : ?>
			<div class="align-center flex justify-center space-x-4 pt-4">
				<form id="follow_form">
					<input type="hidden" name="csrf_token" value="<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>" />
					<input type="hidden" name="follower_user_id" value="<?= $user->getId() ?>">
					<button type="submit" id="follow_btn" class="btn btn-info text-white">
						<?php echo ($isFollow) ? 'フォロー中' : 'フォローする' ?>
					</button>
				</form>
				<button class="btn">メッセージ</button>
			</div>

		<?php endif; ?>
	</div>
</div>

<!-- post -->
<div class="container mx-auto mb-14 flex flex-col items-center justify-center p-4">

	<?php foreach ($posts as $post) : ?>
		<div class="w-full mt-4 flex max-w-lg flex-col space-y-6 overflow-hidden rounded-lg p-6 shadow-md dark:bg-gray-900 dark:text-gray-100 relative hover:bg-gray-100 dark:hover:bg-gray-700">
			<a href="/posts?url=<?= $post->getUrl() ?>" class="absolute h-full w-full top-0 left-0 z-0"></a>
			<div class="flex space-x-4">
				<a href="#" class="z-50">
					<img alt="" src="https://source.unsplash.com/100x100/?portrait" class="h-12 w-12 rounded-full object-cover shadow dark:bg-gray-500" />
				</a>
				<div class="flex flex-col space-y-1">
					<a rel="noopener noreferrer" href="#" class="text-sm font-semibold"><?= $post->getCreatedUser()->getAccountName() ?></a>
					<span class="text-xs dark:text-gray-400">4 hours ago</span>
				</div>
			</div>
			<div>
				<p class="text-sm dark:text-gray-400">
					<?= $post->getContent() ?>
				</p>
			</div>
			<div class="flex flex-wrap justify-between">
				<div class="space-x-2">
					<button aria-label="Share this post" type="button" class="p-2 text-center z-50">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="h-4 w-4 fill-current dark:text-violet-400">
							<path d="M404,344a75.9,75.9,0,0,0-60.208,29.7L179.869,280.664a75.693,75.693,0,0,0,0-49.328L343.792,138.3a75.937,75.937,0,1,0-13.776-28.976L163.3,203.946a76,76,0,1,0,0,104.108l166.717,94.623A75.991,75.991,0,1,0,404,344Zm0-296a44,44,0,1,1-44,44A44.049,44.049,0,0,1,404,48ZM108,300a44,44,0,1,1,44-44A44.049,44.049,0,0,1,108,300ZM404,464a44,44,0,1,1,44-44A44.049,44.049,0,0,1,404,464Z"></path>
						</svg>
					</button>
					<button aria-label="Bookmark this post" type="button" class="p-2 z-50">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="h-4 w-4 fill-current dark:text-violet-400">
							<path d="M424,496H388.75L256.008,381.19,123.467,496H88V16H424ZM120,48V456.667l135.992-117.8L392,456.5V48Z"></path>
						</svg>
					</button>
				</div>
				<div class="flex space-x-2 text-sm dark:text-gray-400">
					<button type="button" class="flex items-center space-x-1.5 p-1 z-50">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="Number of comments" class="h-4 w-4 fill-current dark:text-violet-400">
							<path d="M448.205,392.507c30.519-27.2,47.8-63.455,47.8-101.078,0-39.984-18.718-77.378-52.707-105.3C410.218,158.963,366.432,144,320,144s-90.218,14.963-123.293,42.131C162.718,214.051,144,251.445,144,291.429s18.718,77.378,52.707,105.3c33.075,27.168,76.861,42.13,123.293,42.13,6.187,0,12.412-.273,18.585-.816l10.546,9.141A199.849,199.849,0,0,0,480,496h16V461.943l-4.686-4.685A199.17,199.17,0,0,1,448.205,392.507ZM370.089,423l-21.161-18.341-7.056.865A180.275,180.275,0,0,1,320,406.857c-79.4,0-144-51.781-144-115.428S240.6,176,320,176s144,51.781,144,115.429c0,31.71-15.82,61.314-44.546,83.358l-9.215,7.071,4.252,12.035a231.287,231.287,0,0,0,37.882,67.817A167.839,167.839,0,0,1,370.089,423Z"></path>
							<path d="M60.185,317.476a220.491,220.491,0,0,0,34.808-63.023l4.22-11.975-9.207-7.066C62.918,214.626,48,186.728,48,156.857,48,96.833,109.009,48,184,48c55.168,0,102.767,26.43,124.077,64.3,3.957-.192,7.931-.3,11.923-.3q12.027,0,23.834,1.167c-8.235-21.335-22.537-40.811-42.2-56.961C270.072,30.279,228.3,16,184,16S97.928,30.279,66.364,56.206C33.886,82.885,16,118.63,16,156.857c0,35.8,16.352,70.295,45.25,96.243a188.4,188.4,0,0,1-40.563,60.729L16,318.515V352H32a190.643,190.643,0,0,0,85.231-20.125,157.3,157.3,0,0,1-5.071-33.645A158.729,158.729,0,0,1,60.185,317.476Z"></path>
						</svg>
						<span>30</span>
					</button>
					<button type="button" class="flex items-center space-x-1.5 p-1 z-50">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="Number of likes" class="h-4 w-4 fill-current dark:text-violet-400">
							<path d="M126.638,202.672H51.986a24.692,24.692,0,0,0-24.242,19.434,487.088,487.088,0,0,0-1.466,206.535l1.5,7.189a24.94,24.94,0,0,0,24.318,19.78h74.547a24.866,24.866,0,0,0,24.837-24.838V227.509A24.865,24.865,0,0,0,126.638,202.672ZM119.475,423.61H57.916l-.309-1.487a455.085,455.085,0,0,1,.158-187.451h61.71Z"></path>
							<path d="M494.459,277.284l-22.09-58.906a24.315,24.315,0,0,0-22.662-15.706H332V173.137l9.573-21.2A88.117,88.117,0,0,0,296.772,35.025a24.3,24.3,0,0,0-31.767,12.1L184.693,222.937V248h23.731L290.7,67.882a56.141,56.141,0,0,1,21.711,70.885l-10.991,24.341L300,169.692v48.98l16,16H444.3L464,287.2v9.272L396.012,415.962H271.07l-86.377-50.67v37.1L256.7,444.633a24.222,24.222,0,0,0,12.25,3.329h131.6a24.246,24.246,0,0,0,21.035-12.234L492.835,310.5A24.26,24.26,0,0,0,496,298.531V285.783A24.144,24.144,0,0,0,494.459,277.284Z"></path>
						</svg>
						<span>283</span>
					</button>
				</div>
			</div>
		</div>
	<?php endforeach; ?>

</div>


<?php if ($user->getId() !== $authenticatedUser->getId()) : ?>
	<script>
		let isFollow = Boolean(<?= $isFollow ?>);
	</script>

	<script src="./profile.bundle.js"></script>
<?php endif; ?>