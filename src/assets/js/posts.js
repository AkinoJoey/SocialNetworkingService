import { likePost, deleteLikePost } from "./likeButton";

document.addEventListener("DOMContentLoaded", function () {
	let likeButtons = document.querySelectorAll(".like-btn");
	let deleteButtons = document.querySelectorAll(".delete-btn");
	let path = location.pathname;

	likeButtons.forEach(function (likeBtn) {
		likeBtn.addEventListener("click", function (e) {
			e.preventDefault();

			let numberOfLikesSpan = likeBtn.querySelector(".number-of-likes");
			let numberOfLikes = Number(numberOfLikesSpan.textContent);
			let goodIcon = likeBtn.querySelector(".good-icon");
			let isLike = likeBtn.getAttribute("data-isLike");

			let formData = new FormData();
			let postId = likeBtn.getAttribute("data-post-id");
			formData.append("csrf_token", csrfToken);
			formData.append("post_id", postId);

			let requestUrl = "";

			if (isLike === "1") {
				if (likeBtn.name === "post_like_btn" && path === "/posts") {
					requestUrl = "form/delete-like-post";
				} else {
					requestUrl = "form/delete-like-comment";
				}

				deleteLikePost(
					requestUrl,
					formData,
					likeBtn,
					numberOfLikes,
					numberOfLikesSpan,
					goodIcon,
				);
			} else {
				if (likeBtn.name === "post_like_btn" && path === "/posts") {
					requestUrl = "form/like-post";
				} else {
					requestUrl = "form/like-comment";
				}

				likePost(
					requestUrl,
					formData,
					likeBtn,
					numberOfLikes,
					numberOfLikesSpan,
					goodIcon,
				);
			}
		});
	});

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

				if (deleteBtn.name === "delete_post_btn" && path === "/posts") {
					fetch("delete/post", {
						method: "POST",
						body: formData,
					})
						.then((response) => response.json())
						.then((data) => {
							if (data.status === "success") {
								console.log('test');
								window.location.href = "/";
								
							} else if (data.status === "error") {
								console.error(data.message);
							}
						})
						.catch((error) => {
							alert("An error occurred. Please try again.");
						});
				} else {
					console.log(test);
				}
			});
		});
	});
});
