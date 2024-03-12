import { initDropdowns, initModals } from "flowbite";
import { likePost, deleteLikePost } from "./likeButton";

document.addEventListener("DOMContentLoaded", function () {
	let deleteExecuteBtn = document.getElementById("delete-execute-btn");
	let postLikesMap = new Map();

	function attachEventListeners(likeButtons, deleteButtons, deleteExecuteBtn) {
		likeButtons.forEach(function (likeBtn) {
			likeBtnClickListener(likeBtn);
		});

		deleteButtons.forEach(function (deleteBtn) {
			deleteBtnClickListener(deleteBtn, deleteExecuteBtn);
		});

		// from flowbite
		initDropdowns();
		initModals();
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

			// 別タブの同じ投稿を更新するためにマップにセット
			postLikesMap.set(postId, isLike);
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
	let timelineContainers = document.querySelectorAll(".timeline-container");
	let offsetCounterMap = new Map();
	offsetCounterMap.set("trend", 0);
	offsetCounterMap.set("following", 0);

	fetchPost();

	function fetchPost() {
		fetch(
			`/timeline/${currentTab}?offset=${offsetCounterMap.get(currentTab)}`,
			{
				method: "GET",
			},
		)
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					// let newPosts = document.createElement("div");
					// newPosts.innerHTML = data.htmlString;

					// document
					// 	.getElementById(currentTab + "_container")
					// 	.appendChild(newPosts);

					document.getElementById(currentTab + "_container").innerHTML =
						data.htmlString;

					let likeButtons = document.querySelectorAll(".like-btn");
					let deleteButtons = document.querySelectorAll(".delete-btn");

					// イベントリスナーを再割り当て
					attachEventListeners(likeButtons, deleteButtons, deleteExecuteBtn);

					// offsetを更新
					// TODO: 値を20にする
					offsetCounterMap.set(
						currentTab,
						offsetCounterMap.get(currentTab) + 3,
					);
				} else if (data.status === "error") {
					console.error(data.message);
				}
			})
			.catch((error) => {
				alert("An error occurred. Please try again.");
			});
	}

	tabs.forEach(function (tab) {
		tab.addEventListener("click", function () {
			if (this.classList.contains("tab-active")) {
				return;
			}

			tabs.forEach(function (tab) {
				tab.classList.toggle("tab-active");
			});

			timelineContainers.forEach(function (container) {
				container.classList.toggle("hidden");
			});

			currentTab = tab.dataset.tab;


			if (offsetCounterMap.get(currentTab) === 0) {
				fetchPost();
			}

			let currentTimeline = document.getElementById(currentTab + '_container');
			let likeButtonsToUpdate = [];

			postLikesMap.keys().forEach(function (postId) {
				likeButtonsToUpdate.push(
					currentTimeline.querySelector(
						`button[data-post-id='${postId}'].like-btn`,
					),
				);
			})
			

			likeButtonsToUpdate.forEach(function (likeBtn) {
				console.log(likeBtn);
				console.log(likeBtn.getAttribute('data-post-id'));
				console.log(likeBtn.querySelector(".number-of-likes"));
			})
			
		});
	});

	// window.addEventListener("scroll", function () {
	// 	let documentHeight = document.documentElement.scrollHeight;

	// 	// 現在のスクロール位置
	// 	let scrollTop = window.scrollY || document.documentElement.scrollTop;

	// 	let windowHeight = window.innerHeight;

	// 	// スクロール位置が最下部に近づいているかどうかをチェック
	// 	if (documentHeight - scrollTop <= windowHeight) {
	// 		fetchPost();
	// 	}
	// });
});
