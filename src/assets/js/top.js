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

	let isFetching = false; // フェッチ中かどうかを管理するフラグ
	await fetchPost();

	async function fetchPost() {
		try {
			isFetching = true;
			let postContainer = document.getElementById(currentTab + "_container");
			let loadingContainer = document.createElement("div");
			loadingContainer.classList.add("flex", "justify-center", "mt-8");
			loadingContainer.innerHTML = `
				<svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
					<path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
				</svg>
				<span class="sr-only">Loading...</span>
				
				`;
			postContainer.appendChild(loadingContainer);

			const response = await fetch(
				`/timeline/${currentTab}?offset=${offsetCounterMap.get(currentTab)}`,
				{
					method: "GET",
				},
			);

			const data = await response.json();
			isFetching = false;
			postContainer.removeChild(loadingContainer);

			if (data.status === "success") {
				let newPosts = document.createElement("div");
				newPosts.innerHTML = data.htmlString;

				postContainer.appendChild(newPosts);

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
				offsetCounterMap.set(currentTab, offsetCounterMap.get(currentTab) + 20);
			} else if (data.status === "error") {
				alert(data.message);
			}
		} catch (error) {
			alert("エラーが発生しました");
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
		if (!isFetching && documentHeight - scrollTop <= windowHeight) {
			fetchPost();
		}
	});
});
