<!-- post -->
<div class="container mx-auto mb-14 flex flex-col items-center justify-center p-4">
	<?php foreach ($posts as $post) : ?>
		<?php include(__DIR__ . '/../components/post_card.php') ?>
	<?php endforeach; ?>
</div>

<script>
	let csrfToken = "<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>"
</script>

<script src="/top.bundle.js"></script>
