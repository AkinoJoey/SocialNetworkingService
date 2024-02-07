document.addEventListener("DOMContentLoaded", function () {
    const likePostForm = document.getElementById("like_post_form");
	let like = false;
	let numberOfPostLikeSpan = document.getElementById("number_of_post_like");
	let numberOfPostLike = Number(numberOfPostLikeSpan.textContent);

	likePostForm.addEventListener("submit", function (event) {
		event.preventDefault();

		const formData = new FormData(form);

		if (like) {
			deleteLikePost(formData);

		} else {
			likePost(formData);
		}
	});

	function likePost(formData) {
		fetch("form/like-post", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json()) 
			.then((data) => {
				if (data.status === "success") {
					like = true;
					numberOfPostLike += 1;
					numberOfPostLikeSpan.innerHTML = numberOfPostLike;
				} else if (data.status === "error") {
					// ユーザーにエラーメッセージを表示します
					console.error(data.message);
					alert("Update failed: " + data.message);
				}
			})
			.catch((error) => {
				alert("An error occurred. Please try again.");
			});
	}

	function deleteLikePost(formData) {
		fetch("form/like-post", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					like = false;
					numberOfPostLike -= 1;
					numberOfPostLikeSpan.innerHTML = numberOfPostLike;
				} else if (data.status === "error") {
					// ユーザーにエラーメッセージを表示します
					console.error(data.message);
					alert("Update failed: " + data.message);
				}
			})
			.catch((error) => {
				alert("An error occurred. Please try again.");
			});
	}
});
