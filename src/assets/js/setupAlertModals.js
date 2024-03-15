import { Modal } from "flowbite";

function setupAlertModals(deleteMenuButtons) {
    const alertModalEl = document.getElementById("alert_modal");
	const alertModal = new Modal(alertModalEl);

	const deleteExecuteBtn = document.getElementById("delete-execute-btn");

	deleteMenuButtons.forEach(function (deleteMenuBtn) {
		deleteMenuBtnClickListener(deleteMenuBtn, deleteExecuteBtn, alertModal);
    });
    
	let alertModalHideButtons = document.querySelectorAll(".alert-modal-hide");

	// アラートモーダル
	alertModalHideButtons.forEach(function (alertModalHideBtn) {
		alertModalHideBtn.addEventListener("click", function () {
			alertModal.hide();
		});
	});
}

function deleteMenuBtnClickListener(deleteMenuBtn, deleteExecuteBtn, alertModal) {
	deleteMenuBtn.addEventListener("click", function () {
		alertModal.show();

		deleteExecuteBtn.addEventListener("click", function () {
			let formData = new FormData();
			let postId = deleteMenuBtn.getAttribute("data-post-id");
			formData.append("csrf_token", csrfToken);
			formData.append("post_id", postId);

			let postType = deleteMenuBtn.getAttribute("data-post-type").toLowerCase();
			let requestUrl = `/delete/${postType}`;

			fetch(requestUrl, {
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


export { setupAlertModals };