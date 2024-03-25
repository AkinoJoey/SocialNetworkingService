import { likePost, deleteLikePost } from "./likeButton";
import { setupAlertModals } from "./setupAlertModals";
import { setupDropDowns } from "./setupDropDowns";

document.addEventListener("DOMContentLoaded", async function () {
	function attachEventListeners(
		likeButtons,
		deleteMenuButtons,
		dropdownContainers,
	) {
		likeButtons.forEach(function (likeBtn) {
			likeBtnClickListener(likeBtn);
		});

		setupDropDowns(dropdownContainers);

		// ドロップダウンの中に削除メニューボタンがある場合は、投稿削除のアラートモーダルの初期化。イベントリスナーの設定
		if (deleteMenuButtons) {
			setupAlertModals(deleteMenuButtons);
		}
	}

	function likeBtnClickListener(likeBtn) {
		likeBtn.addEventListener("click", async function () {
			let postId = likeBtn.getAttribute("data-post-id");
			let formData = new FormData();
			formData.append("post_id", postId);
			formData.append("csrf_token", csrfToken);
			let isLike = likeBtn.getAttribute("data-isLike");

			if (isLike === "1") {
				await deleteLikePost("/form/delete-like-post", formData, likeBtn);
			} else {
				await likePost("/form/like-post", formData, likeBtn);
			}
		});
	}

	if (myProfile === false) {
		// フォローボタンの挙動
		const followForm = document.getElementById("follow_form");
		const followerCount = document.getElementById("followerCount");
		let numberOfFollower = Number(followerCount.textContent);
		let followBtn = document.getElementById("follow_btn");

		followForm.addEventListener("submit", function (event) {
			event.preventDefault();

			const formData = new FormData(followForm);

			// isLikeはViews/profile.phpで定義
			if (isFollow) {
				unFollow(formData);
			} else {
				follow(formData);
			}
		});

		function follow(formData) {
			fetch("/form/follow", {
				method: "POST",
				body: formData,
			})
				.then((response) => response.json())
				.then((data) => {
					if (data.status === "success") {
						isFollow = true;
						numberOfFollower += 1;
						followerCount.innerHTML = numberOfFollower;
						followBtn.innerHTML = "フォロー中";
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

		function unFollow(formData) {
			fetch("form/unFollow", {
				method: "POST",
				body: formData,
			})
				.then((response) => response.json())
				.then((data) => {
					if (data.status === "success") {
						isFollow = false;
						numberOfFollower -= 1;
						followerCount.innerHTML = numberOfFollower;
						followBtn.innerHTML = "フォローする";
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
	}

	let offsetCounter = 0;
	let postsContainer = document.getElementById("posts_container");

	await fetchPost();

	async function fetchPost() {
		try {
			const response = await fetch(
				`/profile/posts?offset=${offsetCounter}&username=${username}`,
				{
					method: "GET",
				},
			);

			const data = await response.json();

			if (data.status === "success") {
				let newPosts = document.createElement("div");
				newPosts.innerHTML = data.htmlString;

				postsContainer.appendChild(newPosts);

				let likeButtons = newPosts.querySelectorAll(".like-btn");
				let deleteMenuButtons = newPosts.querySelectorAll(".delete-menu-btn");
				let dropdownContainers = newPosts.querySelectorAll(".post-dropdown");

				// イベントリスナーを割り当て
				attachEventListeners(likeButtons, deleteMenuButtons, dropdownContainers);

				// offsetを更新
				offsetCounter += 20;
			} else if (data.status === "error") {
				console.error(data.message);
			}
		} catch (error) {
			alert("An error occurred. Please try again.");
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
