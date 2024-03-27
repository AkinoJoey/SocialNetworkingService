import { Modal } from "flowbite";
import { switchButtonVisibility } from "./changeLoadingBtn";

function setupAlertModals(deleteMenuButtons) {
	const alertModalEl = document.getElementById("alert_modal");
	const alertModal = new Modal(alertModalEl);

	const deleteExecuteBtn = document.getElementById("delete-execute-btn");
	const loadingBtn = document.getElementById("loading-delete-execute-btn");

	deleteMenuButtons.forEach(function (deleteMenuBtn) {
		deleteMenuBtnClickListener(
			deleteMenuBtn,
			deleteExecuteBtn,
			loadingBtn,
			alertModal,
		);
	});

	let alertModalHideButtons = document.querySelectorAll(".alert-modal-hide");

	// アラートモーダル
	alertModalHideButtons.forEach(function (alertModalHideBtn) {
		alertModalHideBtn.addEventListener("click", function () {
			alertModal.hide();
		});
	});
}

function deleteMenuBtnClickListener(
	deleteMenuBtn,
	deleteExecuteBtn,
	loadingBtn,
	alertModal,
) {
	deleteMenuBtn.addEventListener("click", function () {
		alertModal.show();

		deleteExecuteBtn.addEventListener("click", function () {
			let formData = new FormData();
			let postId = deleteMenuBtn.getAttribute("data-post-id");
			formData.append("csrf_token", csrfToken);
			formData.append("post_id", postId);

			let postType = deleteMenuBtn.getAttribute("data-post-type").toLowerCase();
			let requestUrl = `/delete/${postType}`;

			switchButtonVisibility(deleteExecuteBtn, loadingBtn);

			fetch(requestUrl, {
				method: "POST",
				body: formData,
			})
				.then((response) => response.json())
				.then((data) => {
					if (data.status === "success") {
						location.reload();
					} else if (data.status === "error") {
						switchButtonVisibility(deleteExecuteBtn, loadingBtn);
						alert(data.message);
					}
				})
				.catch((error) => {
					switchButtonVisibility(deleteExecuteBtn, loadingBtn);
					alert("エラーが発生しました");
				});
		});
	});
}

export { setupAlertModals };
