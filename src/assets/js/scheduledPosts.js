import { Modal } from "flowbite";
import { setupAlertModals } from "./setupAlertModals";

document.addEventListener("DOMContentLoaded", function () {
	let previewModalEl = document.getElementById("preview_modal");
	let previewModal = new Modal(previewModalEl);

	let previewHideBtn = document.getElementById("preview_modal_hide_btn");
	let scheduledAtContainer = document.getElementById("scheduled_at_container");
	let previewPostContent = document.getElementById("preview_content");
	let previewMediaContainer = document.getElementById(
		"preview_media_container",
	);

	let scheduledPosts = document.querySelectorAll(".scheduled-post");
	scheduledPosts.forEach(function (scheduledPost) {
		scheduledPost.addEventListener("click", function () {
			let scheduledPostContent = scheduledPost.querySelector(
				".scheduled-post-content",
			).textContent;
			let scheduledAt =
				scheduledPost.querySelector(".scheduled-at").textContent;

			previewPostContent.innerHTML = scheduledPostContent;
			scheduledAtContainer.innerHTML = scheduledAt;
			previewMediaContainer.innerHTML = "";

			// メディアを表示
			let media = scheduledPost.querySelector(".media-item");

			if (media.dataset.mediaPath.trim() !== "") {
				let previewElement;
				console.log(media.dataset.mediaExtension);

				if (media.dataset.mediaExtension === ".mp4") {
					previewElement = document.createElement("video");
					previewElement.controls = true; // コントロールバーを表示する
					previewElement.autoplay = false; // 自動再生を無効にする
					previewElement.muted = true; // 音声をミュートにする（任意）
				} else {
					previewElement = document.createElement("img");
				}

				previewElement.src = media.dataset.mediaPath;
				previewElement.classList.add("w-full");
				previewMediaContainer.appendChild(previewElement);
			}

			previewModal.show();

			previewHideBtn.addEventListener("click", function () {
				previewModal.hide();
			});
		});
	});

	let deleteScheduledPostButtons = document.querySelectorAll(
		".delete-scheduled-post",
	);
	setupAlertModals(deleteScheduledPostButtons);
});
