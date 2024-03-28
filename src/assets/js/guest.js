document.addEventListener("DOMContentLoaded", async function () {
	let offsetCounter = 0;
    let postsContainer = document.getElementById("posts_container");
    
    await fetchPost();

	async function fetchPost() {
		try {
			const response = await fetch(
				`/timeline/guest?offset=${offsetCounter}`,
				{
					method: "GET",
				},
			);

			const data = await response.json();

			if (data.status === "success") {
				let newPosts = document.createElement("div");
				newPosts.innerHTML = data.htmlString;

				postsContainer.appendChild(newPosts);

				// offsetを更新
				offsetCounter += 20;
			} else if (data.status === "error") {
				alert(data.message);
			}
		} catch (error) {
			alert("エラーが発生しました");
		}
	}

	// 無限スクロール
	window.addEventListener("scroll", function () {
		let documentHeight = document.documentElement.scrollHeight;

		// 現在のスクロール位置
		let scrollTop = window.scrollY || document.documentElement.scrollTop;

		let windowHeight = window.innerHeight;

		// スクロール位置が最下部に近づいているかどうかをチェック
		if (documentHeight - scrollTop <= windowHeight) {
			fetchPost();
		}
	});
});
