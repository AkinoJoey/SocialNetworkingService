import { likePost, deleteLikePost } from "./likeButton";

document.addEventListener("DOMContentLoaded", function () {
	let likeButtons = document.querySelectorAll(".like-btn");

	likeButtons.forEach(function (likeBtn) {
		likeBtn.addEventListener("click", function (e) {
			e.preventDefault();
			let numberOfPostLikeSpan = likeBtn.querySelector(".number-of-post-likes");
			let numberOfPostLike = Number(numberOfPostLikeSpan.textContent);
			let goodBtn = likeBtn.querySelector(".good-btn");

			let isLike = likeBtn.getAttribute("data-isLike");
			let postId = likeBtn.getAttribute("data-post-id");

			let formData = new FormData();
			formData.append("post_id", postId);
			formData.append("csrf_token", csrfToken);

			if (isLike === "1") {
				deleteLikePost(
					formData,
					likeBtn,
					numberOfPostLike,
					numberOfPostLikeSpan,
					goodBtn,
				);
			} else {
				likePost(
					formData,
					likeBtn,
					numberOfPostLike,
					numberOfPostLikeSpan,
					goodBtn,
				);
			}
		});
	});
});
