document.addEventListener("DOMContentLoaded", function () {
	let signupForm = document.getElementById("signup_form");

	signupForm.addEventListener("submit", function (e) {
		e.preventDefault();
		let formData = new FormData(signupForm);
		console.log("test");

		document.getElementById("submit_btn").classList.add("hidden");
		document.getElementById("loading_btn").classList.remove("hidden");

		fetch("/form/signup", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === 'success') {
					// バックエンド側でリダイレクト
				} else if (data.status === 'error') {
					document.getElementById("submit_btn").classList.remove("hidden");
					document.getElementById("loading_btn").classList.add("hidden");
					alert(data.message);
				}
			})
			.catch(error => {
				alert('エラーが発生しました');
			});
	});
});
