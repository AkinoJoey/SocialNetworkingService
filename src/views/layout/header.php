<!doctype html>
<html>

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script src="https://kit.fontawesome.com/51efafd327.js" crossorigin="anonymous"></script>
	<link rel="icon" href="/images/favicon.ico" sizes="any">
	<title>ten</title>
</head>

<body>
	<?php if (isset($user) && $user->getEmailVerified()) : ?>
		<?php include(__DIR__ . '/../components/sidebar.php') ?>
		<?php include(__DIR__ . '/../components/bottom_nav.php') ?>
		<?php include(__DIR__ . '/../components/new_post.php') ?>
		<?php include(__DIR__ . '/../components/schedule.php') ?>
		<?php include(__DIR__ . '/../components/logout_modal.php') ?>
	<?php endif; ?>