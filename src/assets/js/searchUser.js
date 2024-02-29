document.addEventListener("DOMContentLoaded", function () {
	let searchForm = document.getElementById("search-form");
	const inputKeyword = document.getElementById("keyword");
	inputKeyword.focus();

	inputKeyword.addEventListener("keydown", function (e) {
		if (e.key === "Enter") {
			e.preventDefault();
			search();
		}
	});

	searchForm.addEventListener("submit", function (e) {
		e.preventDefault();
		search();
	});

	function search() {
		const formData = new FormData(searchForm);
		window.location.href = `/search/user?keyword=${formData.get("keyword")}`;
	}
});
