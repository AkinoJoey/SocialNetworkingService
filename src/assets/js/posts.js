import { likePost, deleteLikePost } from "./likeButton";
import { showFilePreview, checkForm } from "./newPost";

document.addEventListener("DOMContentLoaded", function () {
	let likeButtons = document.querySelectorAll(".like-btn");
	let deleteButtons = document.querySelectorAll(".delete-btn");
	let path = location.pathname;

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

				await deleteLikePost(
					requestUrl,
					formData,
					likeBtn
				);
			} else {
				if (likeBtn.name === "post_like_btn" && path === "/posts") {
					requestUrl = "/form/like-post";
				} else {
					requestUrl = "/form/like-comment";
				}

				await likePost(
					requestUrl,
					formData,
					likeBtn
				);
			}
		});
	});

	const deleteExecuteBtn = document.getElementById("delete-execute-btn");

	deleteButtons.forEach(function (deleteBtn) {
		deleteBtn.addEventListener("click", function () {
			deleteExecuteBtn.addEventListener("click", function () {
				let formData = new FormData();
				let postId = deleteBtn.getAttribute("data-post-id");
				formData.append("csrf_token", csrfToken);

				if (deleteBtn.name === "delete_post_btn" && path === "/posts") {
					formData.append("post_id", postId);
					fetch("delete/post", {
						method: "POST",
						body: formData,
					})
						.then((response) => response.json())
						.then((data) => {
							if (data.status === "success") {
								window.location.href = "/";
							} else if (data.status === "error") {
								console.error(data.message);
							}
						})
						.catch((error) => {
							alert("An error occurred. Please try again.");
						});
				} else if (
					deleteBtn.name === "delete_post_btn" &&
					path === "/comments"
				) {
					formData.append("comment_id", postId);
					fetch("delete/comment", {
						method: "POST",
						body: formData,
					})
						.then((response) => response.json())
						.then((data) => {
							if (data.status === "success") {
								window.location.href = "/";
							} else if (data.status === "error") {
								console.error(data.message);
							}
						})
						.catch((error) => {
							alert("An error occurred. Please try again.");
						});
				} else {
					formData.append("comment_id", postId);
					fetch("delete/comment", {
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
				}
			});
		});
	});


	const fileInputIcon = document.getElementById("reply-file-input-icon");
	let fileInput = document.getElementById("reply-file-input");
	let previewContainer = document.getElementById("reply-previewContainer");
	let textInput = document.getElementById("reply-content");
	let submitBtn = document.getElementById('reply-submit');

	// 画像アイコンをクリックしたとき
	fileInputIcon.addEventListener("click", function () {
		fileInput.addEventListener("change", function (event) {
			showFilePreview(event, previewContainer, textInput, fileInput, submitBtn);

			checkForm(textInput, fileInput, submitBtn);
		})

		fileInput.click();
	});

	textInput.addEventListener("input", function () {
		checkForm(textInput, fileInput, submitBtn);
	});

	let replyForm = document.getElementById("reply-form");

	// リプライを送信したとき
	replyForm.addEventListener("submit", function (e) {
		e.preventDefault();
		let formData = new FormData(replyForm);
		let type = location.pathname === "/posts" ? "post" : "comment";
		formData.append("type_reply_to", type);
		formData.append('media', fileInput.files[0]);

		fetch("/form/reply", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					location.reload();
				} else if (data.status === "error") {
					alert(data.message);
				}
			})
			.catch((error) => {
				alert("An error occurred. Please try again.");
			});
	});
});
