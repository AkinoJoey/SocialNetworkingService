import { likePost, deleteLikePost } from "./likeButton";
import { setupDropDowns } from "./setupDropDowns";
import { setupAlertModals } from "./setupAlertModals";

document.addEventListener("DOMContentLoaded", async function () {
	let postLikesMap = new Map(); // int postId => [string isLike, int numberOfLikes];

	function attachEventListeners(
		likeButtons,
		deleteMenuButtons,
		dropdownContainers,
	) {
		likeButtons.forEach(function (likeBtn) {
			likeBtnClickListener(likeBtn);
		});

		setupDropDowns(dropdownContainers);

		setupAlertModals(deleteMenuButtons);
	}

	function likeBtnClickListener(likeBtn) {
		likeBtn.addEventListener("click", async function () {
			let isLike = likeBtn.getAttribute("data-isLike");
			let postId = likeBtn.getAttribute("data-post-id");

			let formData = new FormData();
			formData.append("post_id", postId);
			formData.append("csrf_token", csrfToken);

			if (isLike === "1") {
				await deleteLikePost("/form/delete-like-post", formData, likeBtn);
			} else {
				await likePost("/form/like-post", formData, likeBtn);
			}

			let numberOfLikesSpan = likeBtn.querySelector(".number-of-likes");
			let numberOfLikes = Number(numberOfLikesSpan.textContent);
			postLikesMap.set(postId, [
				likeBtn.getAttribute("data-isLike"),
				numberOfLikes,
			]);
		});
	}


	let offsetCounterMap = new Map();
	offsetCounterMap.set("trend", 0);
	offsetCounterMap.set("following", 0);

	await fetchPost();

	async function fetchPost() {
		try {
			const response = await fetch(
				`/timeline/${currentTab}?offset=${offsetCounterMap.get(currentTab)}`,
				{
					method: "GET",
				},
			);

			const data = await response.json();

			if (data.status === "success") {
				let newPosts = document.createElement("div");
				newPosts.innerHTML = data.htmlString;

				document
					.getElementById(currentTab + "_container")
					.appendChild(newPosts);

				let likeButtons = newPosts.querySelectorAll(".like-btn");
				let deleteMenuButtons = newPosts.querySelectorAll(".delete-menu-btn");
				let dropdownContainers = newPosts.querySelectorAll(".post-dropdown");

				// イベントリスナーを割り当て
				attachEventListeners(
					likeButtons,
					deleteMenuButtons,
					dropdownContainers,
				);

				// offsetを更新
				// TODO: 値を20にする
				offsetCounterMap.set(currentTab, offsetCounterMap.get(currentTab) + 3);
			} else if (data.status === "error") {
				console.error(data.message);
			}
		} catch (error) {
			alert("An error occurred. Please try again.");
		}
	}

	// タブの切替
	let tabs = document.querySelectorAll(".tab");
	let timelineContainers = document.querySelectorAll(".timeline-container");

	tabs.forEach(function (tab) {
		tab.addEventListener("click", async function () {
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
				// 最初は最新のデータをとってくる
				await fetchPost();
			} else {
				// 1度データを取ってきた後は、いいねボタンが押された情報を更新する
				updateLikeButtonsInOpenedTab();
			}
		});
	});

	function updateLikeButtonsInOpenedTab() {
		let currentTimeline = document.getElementById(currentTab + "_container");
		let likeButtonsToUpdate = [];

		// いいねボタンが押された投稿のボタンをすべて配列にいれる
		postLikesMap.keys().forEach(function (postId) {
			let likeBtn = currentTimeline.querySelector(
				`button[data-post-id='${postId}'].like-btn`,
			);

			if (likeBtn !== null) {
				likeButtonsToUpdate.push(likeBtn);
			}
		});

		if (likeButtonsToUpdate.length !== 0) {
			likeButtonsToUpdate.forEach(function (likeBtn) {
				let postLikesValue = postLikesMap.get(
					likeBtn.getAttribute("data-post-id"),
				);
				let isLike = postLikesValue[0];

				likeBtn.setAttribute("data-isLike", isLike);

				let goodIcon = likeBtn.querySelector(".good-icon");

				if (isLike === "1") {
					goodIcon.classList.add("fill-blue-600");
				} else if (isLike === "0") {
					goodIcon.classList.remove("fill-blue-600");
				}

				let numberOfLikes = postLikesValue[1];
				let numberOfLikesSpan = likeBtn.querySelector(".number-of-likes");
				numberOfLikesSpan.innerHTML = numberOfLikes;
			});

			// 更新完了後に空にする
			postLikesMap.clear();
			likeButtonsToUpdate.length = 0;
		}
	}

	// 無限スクロール
	window.addEventListener("scroll", function () {
		let documentHeight = document.documentElement.scrollHeight;

		// 現在のスクロール位置
		let scrollTop = window.scrollY || document.documentElement.scrollTop;

		let windowHeight = window.innerHeight;

		// スクロール位置が最下部に近づいているかどうかをチェック
		if (documentHeight - scrollTop <= windowHeight) {
			fetchPost();
		}
	});
});
