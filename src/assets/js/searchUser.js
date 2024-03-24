document.addEventListener("DOMContentLoaded", function () {
	const inputKeyword = document.getElementById("keyword");
	inputKeyword.focus();
	search("");

	inputKeyword.addEventListener("input", function (e) {
		// if (e.key === "Enter") {
		// 	e.preventDefault();
		// 	search();
		// }

		let keyword = e.target.value;
		search(keyword);
	});

	let usersContainer = document.getElementById("users_container");

	function search(keyword) {
		fetch(`/search/user-list?keyword=${keyword}`, {
			method: "GET",
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					usersContainer.innerHTML = data.htmlString;
				} else if (data.status === "error") {
					alert(data.message);
				}
			})
			.catch((error) => {
				alert("エラーが発生しました。更新してみてください");
			});
	}
});
