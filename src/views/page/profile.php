<!-- profile -->
<div class="container mx-auto flex justify-center p-4">
	<div class="w-full rounded-lg p-6 shadow-md dark:bg-gray-900 dark:text-gray-100 sm:m-12 lg:mx-40 max-w-2xl">
		<div class="flex flex-col">
			<div class="w-full flex flex-row">
				<div class="w-1/3">
					<img src="<?= $profile->getProfileImagePath() === null ? '/images/user_default_portrait.png' : '/uploads/' . substr($profile->getProfileImagePath(), 0, 2) . '/' .  $profile->getProfileImagePath() . $profile->getExtension() ?>" alt="user avatar" class="h-16 w-16 rounded-full object-cover shadow dark:bg-gray-500" width="75px" height="75px" />
				</div>
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
				<h4 class="text-lg font-semibold mt-1">
					<?= htmlspecialchars($user->getAccountName()) ?>
				</h4>
				<h4 class="text-sm text-gray-500 dark:text-gray-400">
					<?= "@" . htmlspecialchars($user->getUsername()) ?>
				</h4>
				<p class="dark:text-gray-400">
					<?= $profile->getDescription() !== null? nl2br(htmlspecialchars($profile->getDescription())): null ?>
				</p>
			</div>
		</div>
		<?php if ($user->getId() === $authenticatedUser->getId()) : ?>
			<div class="flex justify-center space-x-4 pt-4">
				<a class="flex" href="/profile/edit">
					<button class="btn btn-xs btn-active">
						プロフィール
					</button>
				</a>
				<button data-modal-target="logout_modal" data-modal-show="logout_modal" type="button" class="logout-btn btn btn-xs btn-active">
					ログアウト
				</button>
				<button data-user-id="<?= $user->getId() ?>" data-modal-target="user_delete_modal" data-modal-show="user_delete_modal" type="button" class="user-delete btn btn-xs btn-error">
					アカウントを削除
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
	<div id="posts_container"></div>
</div>

<?php include(__DIR__ . '/../components/alert_modal.php') ?>
<?php include(__DIR__ . '/../components/speed_dial.php') ?>
<?php include(__DIR__ . '/../components/user_delete_modal.php') ?>


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
	let username = "<?= $user->getUsername() ?>"
</script>
<script src="/profile.bundle.js"></script>