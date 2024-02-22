import { likePost, deleteLikePost } from "./likeButton";

document.addEventListener("DOMContentLoaded", function () {
	// いいねボタンの挙動
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
});
