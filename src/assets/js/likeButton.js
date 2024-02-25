export function likePost(
	requestUrl,
	formData,
	likeBtn,
	numberOfLikes,
	numberOfLikesSpan,
	goodIcon,
) {
	fetch(requestUrl, {
		method: "POST",
		body: formData,
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.status === "success") {
				likeBtn.setAttribute("data-isLike", "1");
				numberOfLikes += 1;
				numberOfLikesSpan.innerHTML = numberOfLikes;
				goodIcon.classList.add("fill-blue-600");
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
	requestUrl,
	formData,
	likeBtn,
	numberOfLikes,
	numberOfLikesSpan,
	goodIcon,
) {
	fetch(requestUrl, {
		method: "POST",
		body: formData,
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.status === "success") {
				likeBtn.setAttribute("data-isLike", "0");
				numberOfLikes -= 1;
				numberOfLikesSpan.innerHTML = numberOfLikes;
				goodIcon.classList.remove("fill-blue-600");
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
