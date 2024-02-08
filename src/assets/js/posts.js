document.addEventListener("DOMContentLoaded", function () {
    const likePostForm = document.getElementById("like_post_form");
	let numberOfPostLikeSpan = document.getElementById("number_of_post_like");
	let numberOfPostLike = Number(numberOfPostLikeSpan.textContent);
	let goodBtn = document.getElementById('good_btn');
	let  path = location.pathname;

	likePostForm.addEventListener("submit", function (event) {
		event.preventDefault();

		const formData = new FormData(likePostForm);

		// isLikeはViews/posts.phpで定義
		if (isLike) {
			let resource = (path === '/posts') ? "form/delete-like-post" : (path === '/comments') ? "form/delete-like-comment" : '';

			deleteLikePost(resource, formData);

		} else {
			let resource = (path === '/posts') ? "form/like-post" : (path === '/comments') ? "form/like-comment" : '';

			likePost(resource,formData);
		}
	});

	function likePost(resource, formData) {
		fetch(resource, {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json()) 
			.then((data) => {
				if (data.status === "success") {
					isLike = true;
					numberOfPostLike += 1;
					numberOfPostLikeSpan.innerHTML = numberOfPostLike;
					goodBtn.classList.add('fill-blue-700');
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

	function deleteLikePost(resource, formData) {
		fetch(resource, {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					isLike = false;
					numberOfPostLike -= 1;
					numberOfPostLikeSpan.innerHTML = numberOfPostLike;
					goodBtn.classList.remove('fill-blue-700');
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
