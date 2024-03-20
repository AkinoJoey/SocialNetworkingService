document.addEventListener("DOMContentLoaded", function () {
	let passwordForm = document.getElementById("password_form");

	passwordForm.addEventListener("submit", function (e) {
		e.preventDefault();
		let formData = new FormData(passwordForm);

		fetch("/form/verify/forgot_password", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					window.location.href = "/login";
				} else if (data.status === "error") {
					alert(data.message);
				}
			})
			.catch((error) => {
				alert("An error occurred. Please try again.");
			});
	});
});
