<!-- profile -->
<div class="container mx-auto flex justify-center p-4">
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
				<a href="direct?url=<?= $dmUrl ?>" class="btn">メッセージ</a>
			</div>

		<?php endif; ?>
	</div>
</div>

<!-- post -->
<div class="container mx-auto mb-14 flex flex-col items-center justify-center p-4">

	<?php foreach ($posts as $post) : ?>
		<?php include(__DIR__ . '/../components/post_card.php') ?>
	<?php endforeach; ?>

</div>

<?php include(__DIR__ . '/../components/alert-modal.php') ?>

<?php if ($user->getId() !== $authenticatedUser->getId()) : ?>
	<script>
		let myProfile = false;
		let isFollow = Boolean(<?= $isFollow ?>);
	</script>

<?php else : ?>
	<script>
		myProfile = true;
	</script>
<?php endif; ?>

<script>
	let csrfToken = "<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>"
</script>
<script src="/profile.bundle.js"></script>