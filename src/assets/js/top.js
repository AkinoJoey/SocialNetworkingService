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
});
