<!-- timeline tab -->
<?php include(__DIR__ . '/../components/timeline_tab.php') ?>

<!-- post -->
<div class="container mx-auto mb-14 flex flex-col items-center justify-center p-4 mt-8">
	<div id="trend_container" class="timeline-container <?php if ($tabActive !== 'trend') echo 'hidden' ?>">
	</div>

	<div id="following_container" class="timeline-container <?php if ($tabActive !== 'following') echo 'hidden' ?>">
	</div>

	<?php include(__DIR__ . '/../components/alert_modal.php') ?>
	<?php include(__DIR__ . '/../components/speed_dial.php') ?>

	<script>
		let csrfToken = "<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>"
		let currentTab = "<?= $tabActive ?>"
	</script>

	<script src="/top.bundle.js"></script>