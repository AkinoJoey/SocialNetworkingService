import { switchButtonVisibility } from "./changeLoadingBtn";

document.addEventListener("DOMContentLoaded", function () {
	let passwordForm = document.getElementById("password_form");
	let submitBtn = document.getElementById('submit_btn');
	let loadingBtn = document.getElementById('loading_btn');

	passwordForm.addEventListener("submit", function (e) {
		e.preventDefault();
		let formData = new FormData(passwordForm);

		switchButtonVisibility(submitBtn, loadingBtn);

		fetch("/form/verify/forgot_password", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					window.location.href = "/login";
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
});
