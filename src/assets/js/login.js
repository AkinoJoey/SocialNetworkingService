import { switchButtonVisibility } from "./changeLoadingBtn";

document.addEventListener("DOMContentLoaded", function () {
	let loginForm = document.getElementById("login_form");
	let submitBtn = document.getElementById("submit_btn");
	let loadingBtn = document.getElementById("loading_btn");

	loginForm.addEventListener("submit", function (e) {
		e.preventDefault();
		let formData = new FormData(loginForm);

		switchButtonVisibility(submitBtn, loadingBtn);

		fetch("/form/login", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					window.location.href = "/";
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
