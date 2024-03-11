import { initDropdowns, initModals } from "flowbite";
import { likePost, deleteLikePost } from "./likeButton";

document.addEventListener("DOMContentLoaded", function () {
	let likeButtons = document.querySelectorAll(".like-btn");
	let deleteButtons = document.querySelectorAll(".delete-btn");
	let deleteExecuteBtn = document.getElementById("delete-execute-btn");

	attachEventListeners(likeButtons, deleteButtons, deleteExecuteBtn);

	function attachEventListeners(likeButtons, deleteButtons, deleteExecuteBtn) {
		likeButtons.forEach(function (likeBtn) {
			likeBtnClickListener(likeBtn);
		});

		deleteButtons.forEach(function (deleteBtn) {
			deleteBtnClickListener(deleteBtn, deleteExecuteBtn);
		});
	}

	function likeBtnClickListener(likeBtn) {
		likeBtn.addEventListener("click", function () {
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
	}

	function deleteBtnClickListener(deleteBtn, deleteExecuteBtn) {
		deleteBtn.addEventListener("click", function () {
			deleteExecuteBtn.addEventListener("click", function () {
				let formData = new FormData();
				let postId = deleteBtn.getAttribute("data-post-id");
				formData.append("csrf_token", csrfToken);
				formData.append("post_id", postId);

				fetch("/delete/post", {
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
	}

	// タブの切替
	let tabs = document.querySelectorAll(".tab");
	let timelineContainer = document.getElementById("timeline_container");

	tabs.forEach(function (tab) {
		tab.addEventListener("click", function () {

			fetch(`/timeline/${tab.dataset.tab}`, {
				method: "GET",
			})
				.then((response) => response.json())
				.then((data) => {
					if (data.status === "success") {
						timelineContainer.innerHTML = data.htmlString;

						let likeButtons = document.querySelectorAll(".like-btn");
						let deleteButtons = document.querySelectorAll(".delete-btn");

						// イベントリスナーを再割り当て
						attachEventListeners(likeButtons, deleteButtons, deleteExecuteBtn);

						// イベントリスナー再割り当て from flowbite
						initDropdowns();
						initModals();

					} else if (data.status === "error") {
						console.error(data.message);
					}
				})
				.catch((error) => {
					alert("An error occurred. Please try again.");
				});

			tabs.forEach(function (t) {
				t.classList.remove(
					"text-white",
					"bg-gray-900",
					"dark:bg-gray-300",
					"dark:text-gray-900",
				);
				t.classList.add(
					"text-gray-900",
					"hover:bg-gray-200",
					"dark:text-white",
					"dark:hover:bg-gray-700",
				);
			});

			tab.classList.remove(
				"text-gray-900",
				"hover:bg-gray-200",
				"dark:text-white",
				"dark:hover:bg-gray-700",
			);
			tab.classList.add(
				"text-white",
				"bg-gray-900",
				"dark:bg-gray-300",
				"dark:text-gray-900",
			);
		});
	});
});
