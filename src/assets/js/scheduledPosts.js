import { Modal } from "flowbite";

document.addEventListener("DOMContentLoaded", function () {
	let postModalEl = document.getElementById("post_modal");
	let postModal = new Modal(postModalEl);
	let scheduledPosts = document.querySelectorAll(".scheduled-post");
	let deleteScheduledPosts = document.querySelectorAll(
		".delete-scheduled-post",
	);
	let newPostContent = document.getElementById("content");

	scheduledPosts.forEach(function (scheduledPost) {
		scheduledPost.addEventListener("click", function () {
			let scheduledPostContent = scheduledPost.querySelector(
				".scheduled-post-content",
            ).textContent;
            let scheduledAt = scheduledPost.querySelector(".scheduled-at").textContent;

			postModal.show();

			let scheduledAtContainer = document.getElementById(
				"scheduledAt_container",
			);
            newPostContent.value = scheduledPostContent;
            scheduledAtContainer.innerHTML = scheduledAt;
            console.log(getScheduledAt());
		});
	});
});
