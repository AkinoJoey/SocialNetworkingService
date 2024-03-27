import { switchButtonVisibility } from "./changeLoadingBtn";
import { likePost, deleteLikePost } from "./likeButton";
import { showFilePreview, checkForm } from "./newPost";
import { setupAlertModals } from "./setupAlertModals";
import { setupDropDowns } from "./setupDropDowns";

document.addEventListener("DOMContentLoaded", async function () {
	let likeButtons = document.querySelectorAll(".like-btn");
	let deleteMenuButtons = document.querySelectorAll(".delete-menu-btn");
	let dropdownContainers = document.querySelectorAll(".post-dropdown");
	let path = location.pathname;

	attachEventListeners(likeButtons, deleteMenuButtons, dropdownContainers);

	function attachEventListeners(
		likeButtons,
		deleteMenuButtons,
		dropdownContainers,
	) {
		likeButtons.forEach(function (likeBtn) {
			likeBtn.addEventListener("click", async function () {
				let formData = new FormData();
				let postId = likeBtn.getAttribute("data-post-id");
				formData.append("csrf_token", csrfToken);
				formData.append("post_id", postId);

				let requestUrl = "";
				let isLike = likeBtn.getAttribute("data-isLike");

				if (isLike === "1") {
					if (likeBtn.name === "post_like_btn" && path === "/posts") {
						requestUrl = "/form/delete-like-post";
					} else {
						requestUrl = "/form/delete-like-comment";
					}

					await deleteLikePost(requestUrl, formData, likeBtn);
				} else {
					if (likeBtn.name === "post_like_btn" && path === "/posts") {
						requestUrl = "/form/like-post";
					} else {
						requestUrl = "/form/like-comment";
					}

					await likePost(requestUrl, formData, likeBtn);
				}
			});
		});

		setupDropDowns(dropdownContainers);

		if (deleteMenuButtons) {
			setupAlertModals(deleteMenuButtons);
		}
	}

	const fileInputIcon = document.getElementById("reply-file-input-icon");
	let fileInput = document.getElementById("reply-file-input");
	let previewContainer = document.getElementById("reply-previewContainer");
	let textInput = document.getElementById("reply-content");
	let submitBtn = document.getElementById("reply-submit");

	// 画像アイコンをクリックしたとき
	fileInputIcon.addEventListener("click", function () {
		fileInput.addEventListener("change", function (event) {
			showFilePreview(event, previewContainer, textInput, fileInput, submitBtn);

			checkForm(textInput, fileInput, submitBtn);
		});

		fileInput.click();
	});

	textInput.addEventListener("input", function () {
		checkForm(textInput, fileInput, submitBtn);
	});

	let replyForm = document.getElementById("reply-form");
	let loadingBtn = document.getElementById("reply-loading-btn");
	// リプライを送信したとき
	replyForm.addEventListener("submit", function (e) {
		e.preventDefault();
		let formData = new FormData(replyForm);
		let type = location.pathname === "/posts" ? "post" : "comment";
		formData.append("type_reply_to", type);
		formData.append("media", fileInput.files[0]);

		switchButtonVisibility(submitBtn, loadingBtn);

		fetch("/form/reply", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					location.reload();
				} else if (data.status === "error") {
					switchButtonVisibility(submitBtn, loadingBtn);

					alert(data.message);
				}
			})
			.catch((error) => {
				switchButtonVisibility(submitBtn, loadingBtn);

				alert("エラーが発生しました");
			});
	});

	let offsetCounter = 0;
	let commentsContainer = document.getElementById("comments_container");

	await fetchComments();

	async function fetchComments() {
		let typeReplyTo = location.pathname.slice(1, location.pathname.length - 1);

		try {
			const response = await fetch(
				`/posts/comments?type_reply_to=${typeReplyTo}&post_id=${postId}&offset=${offsetCounter}`,
				{
					method: "GET",
				},
			);

			const data = await response.json();

			if (data.status === "success") {
				let newComments = document.createElement("div");
				newComments.innerHTML = data.htmlString;

				commentsContainer.appendChild(newComments);

				let likeButtons = newComments.querySelectorAll(".like-btn");
				let deleteMenuButtons =
					newComments.querySelectorAll(".delete-menu-btn");
				let dropdownContainers = newComments.querySelectorAll(".post-dropdown");

				// イベントリスナーを割り当て
				attachEventListeners(
					likeButtons,
					deleteMenuButtons,
					dropdownContainers,
				);

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
			fetchComments();
		}
	});
});
