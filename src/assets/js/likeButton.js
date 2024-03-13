async function likePost(requestUrl,formData, likeBtn) {
	try {
		const response = await fetch(requestUrl, {
			method: "POST",
			body: formData,
		});

		const data = await response.json();

		if (data.status === "success") {
			likeBtn.setAttribute("data-isLike", "1");
			let numberOfLikesSpan = likeBtn.querySelector(".number-of-likes");
			let numberOfLikes = Number(numberOfLikesSpan.textContent);

			numberOfLikes += 1;
			numberOfLikesSpan.innerHTML = numberOfLikes;
			let goodIcon = likeBtn.querySelector(".good-icon");

			goodIcon.classList.add("fill-blue-600");
		} else if (data.status === "error") {
			// ユーザーにエラーメッセージを表示します
			console.error(data.message);
			alert("Update failed: " + data.message);
		}
	} catch (error) {
		alert("An error occurred. Please try again.");
	}
}
async function deleteLikePost(requestUrl, formData, likeBtn) {
	try {
		const response = await fetch(requestUrl, {
			method: "POST",
			body: formData,
		});

		const data = await response.json();

		if (data.status === "success") {
			likeBtn.setAttribute("data-isLike", "0");
			let numberOfLikesSpan = likeBtn.querySelector(".number-of-likes");
			let numberOfLikes = Number(numberOfLikesSpan.textContent);

			numberOfLikes -= 1;
			numberOfLikesSpan.innerHTML = numberOfLikes;
			let goodIcon = likeBtn.querySelector(".good-icon");

			goodIcon.classList.remove("fill-blue-600");
		} else if (data.status === "error") {
			// ユーザーにエラーメッセージを表示します
			console.error(data.message);
			alert("Update failed: " + data.message);
		}
	} catch (error) {
		alert("An error occurred. Please try again.");
	}
}


export { likePost, deleteLikePost };
