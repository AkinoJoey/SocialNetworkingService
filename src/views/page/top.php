<!-- timeline tab -->
<?php include(__DIR__ . '/../components/timeline_tab.php') ?>

<!-- post -->
<div class="container mx-auto mb-14 flex flex-col items-center justify-center p-4">
	<?php foreach ($posts as $post) : ?>
		<?php include(__DIR__ . '/../components/post_card.php') ?>
	<?php endforeach; ?>
</div>

<?php include(__DIR__ . '/../components/alert_modal.php') ?>
<?php include(__DIR__ . '/../components/speed_dial.php') ?>

<script>
	let csrfToken = "<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>"
</script>

<script src="/top.bundle.js"></script>