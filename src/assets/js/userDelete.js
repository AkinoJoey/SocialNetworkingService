import { switchButtonVisibility } from "./changeLoadingBtn";

function setupUserDelete(userDeleteBtn) {
	let userDeleteExCuteBtn = document.getElementById("user_delete_execute_btn");
	let loadingBtn = document.getElementById("loading_user_delete_execute_btn");

	userDeleteExCuteBtn.addEventListener("click", function () {
		let formData = new FormData();
		let userId = userDeleteBtn.getAttribute("data-user-id");
		formData.append("csrf_token", csrfToken);
		formData.append("user_id", userId);

		switchButtonVisibility(userDeleteExCuteBtn, loadingBtn);

		fetch("/user/delete", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					window.location.href = "/";
				} else if (data.status === "error") {
					switchButtonVisibility(userDeleteExCuteBtn, loadingBtn);
					alert(data.message);
				}
			})
			.catch((error) => {
				switchButtonVisibility(userDeleteExCuteBtn, loadingBtn);
				alert("エラーが発生しました");
			});
	});
}

export { setupUserDelete };
