import { likePost, deleteLikePost } from "./likeButton";
import { setupAlertModals } from "./setupAlertModals";
import { setupDropDowns } from "./setupDropDowns";
import { setupUserDelete } from "./userDelete";

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
						alert(data.message);
					}
				})
				.catch((error) => {
					alert("エラーが発生しました");
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

	let isFetching = false; // フェッチ中かどうかを管理するフラグ
	await fetchPost();

	async function fetchPost() {
		try {
			isFetching = true;
			let loadingContainer = document.createElement("div");
			loadingContainer.classList.add("flex", "justify-center", "mt-8");
			loadingContainer.innerHTML = `
				<svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
					<path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
				</svg>
				<span class="sr-only">Loading...</span>
				
				`;
			postsContainer.appendChild(loadingContainer);
			const response = await fetch(
				`/profile/posts?offset=${offsetCounter}&username=${username}`,
				{
					method: "GET",
				},
			);

			const data = await response.json();
			isFetching = false;
			postsContainer.removeChild(loadingContainer);

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
		if (!isFetching && documentHeight - scrollTop <= windowHeight) {
			fetchPost();
		}
	});

	let userDeleteBtn = document.querySelector(".user-delete");
	if (userDeleteBtn) {
		setupUserDelete(userDeleteBtn);
	}
});
