<!-- post -->
<div class="container mx-auto mb-14 flex flex-col items-center justify-center p-4">
	<?php foreach ($posts as $post) : ?>
		<?php include(__DIR__ . '/../components/post_card.php') ?>
	<?php endforeach; ?>
</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		let likeButtons = document.querySelectorAll(".like-btn");

		likeButtons.forEach(function(likeBtn) {
			likeBtn.addEventListener("click", function(e) {
				e.preventDefault();

				let isLike = likeBtn.getAttribute("data-isLike");

				if (isLike === 1) {

				}

				let postId = likeBtn.getAttribute("data-post-id");
				let csrfToken = likeBtn.getAttribute("data-token");
				let formData = new FormData();
				formData.append("post_id", postId);
				formData.append("csrf_token", csrfToken);

				if (isRead === "1") {
					window.location.href = notification.href;
				} else {
					fetch("/update-isRead", {
							method: "POST",
							body: formData,
						})
						.then((response) => response.json())
						.then((data) => {
							if (data.status === "success") {
								window.location.href = notification.href;
							} else if (data.status === "error") {
								alert(data.message);
							}
						})
						.catch((error) => {
							alert("An error occurred. Please try again.");
						});
				}
			});
		});

		function likePost(resource, formData) {
			fetch(resource, {
					method: "POST",
					body: formData,
				})
				.then((response) => response.json())
				.then((data) => {
					if (data.status === "success") {
						isLike = true;
						numberOfPostLike += 1;
						numberOfPostLikeSpan.innerHTML = numberOfPostLike;
						goodBtn.classList.add('fill-blue-700');
					} else if (data.status === "error") {
						// ユーザーにエラーメッセージを表示します
						console.error(data.message);
						alert("Update failed: " + data.message);
					}
				})
				.catch((error) => {
					alert("An error occurred. Please try again.");
				});
		}

		function deleteLikePost(resource, formData) {
			fetch(resource, {
					method: "POST",
					body: formData,
				})
				.then((response) => response.json())
				.then((data) => {
					if (data.status === "success") {
						isLike = false;
						numberOfPostLike -= 1;
						numberOfPostLikeSpan.innerHTML = numberOfPostLike;
						goodBtn.classList.remove('fill-blue-700');
					} else if (data.status === "error") {
						// ユーザーにエラーメッセージを表示します
						console.error(data.message);
						alert("Update failed: " + data.message);
					}
				})
				.catch((error) => {
					alert("An error occurred. Please try again.");
				});
		}
	})
</script>