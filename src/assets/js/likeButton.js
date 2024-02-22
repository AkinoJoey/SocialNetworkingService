export function likePost(
	formData,
	likeBtn,
	numberOfPostLike,
	numberOfPostLikeSpan,
	goodBtn,
) {
	fetch("/form/like-post", {
		method: "POST",
		body: formData,
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.status === "success") {
				likeBtn.setAttribute("data-isLike", "1");
				numberOfPostLike += 1;
				numberOfPostLikeSpan.innerHTML = numberOfPostLike;
				goodBtn.classList.add("fill-blue-600");
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

export function deleteLikePost(
	formData,
	likeBtn,
	numberOfPostLike,
	numberOfPostLikeSpan,
	goodBtn,
) {
	fetch("/form/delete-like-post", {
		method: "POST",
		body: formData,
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.status === "success") {
				likeBtn.setAttribute("data-isLike", "0");
				numberOfPostLike -= 1;
				numberOfPostLikeSpan.innerHTML = numberOfPostLike;
				goodBtn.classList.remove("fill-blue-600");
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
