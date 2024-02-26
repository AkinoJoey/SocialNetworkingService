import { likePost, deleteLikePost } from "./likeButton";

document.addEventListener("DOMContentLoaded", function () {
	let likeButtons = document.querySelectorAll(".like-btn");

	likeButtons.forEach(function (likeBtn) {
		likeBtn.addEventListener("click", function (e) {
			e.preventDefault();
			let numberOfLikesSpan = likeBtn.querySelector(".number-of-likes");
			let numberOfLikes = Number(numberOfLikesSpan.textContent);
			let goodIcon = likeBtn.querySelector(".good-icon");

			let isLike = likeBtn.getAttribute("data-isLike");
			let postId = likeBtn.getAttribute("data-post-id");

			let formData = new FormData();
			formData.append("post_id", postId);
			formData.append("csrf_token", csrfToken);

			if (isLike === "1") {
				deleteLikePost(
					"/form/delete-like-post",
					formData,
					likeBtn,
					numberOfLikes,
					numberOfLikesSpan,
					goodIcon,
				);
			} else {
				likePost(
					"/form/like-post",
					formData,
					likeBtn,
					numberOfLikes,
					numberOfLikesSpan,
					goodIcon,
				);
			}
		});
	});

	let deleteButtons = document.querySelectorAll(".delete-btn");
	const alertModalEl = document.getElementById("alert-modal");
	const alertModal = new Modal(alertModalEl);
	const deleteExecuteBtn = document.getElementById("delete-execute-btn");

	deleteButtons.forEach(function (deleteBtn) {
		deleteBtn.addEventListener("click", function (e) {
			e.preventDefault();
			alertModal.show();

			deleteExecuteBtn.addEventListener("click", function () {
				let formData = new FormData();
				let postId = deleteBtn.getAttribute("data-post-id");
				formData.append("csrf_token", csrfToken);
				formData.append("post_id", postId);

				fetch("delete/post", {
					method: "POST",
					body: formData,
				})
					.then((response) => response.json())
					.then((data) => {
						if (data.status === "success") {
							location.reload();
						} else if (data.status === "error") {
							console.error(data.message);
						}
					})
					.catch((error) => {
						alert("An error occurred. Please try again.");
					});
			});
		});
	});
});
