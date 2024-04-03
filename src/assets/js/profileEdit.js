import { switchButtonVisibility } from "./changeLoadingBtn";

document.addEventListener("DOMContentLoaded", function () {
	let fileInput = document.getElementById("file-input");
	let userPortrait = document.getElementById("user_portrait");

	// 画像をクリックした時の処理
	userPortrait.addEventListener("click", function () {
		// file inputを起動
		fileInput.click();
	});

	// ファイルが選択された時の処理
	fileInput.addEventListener("change", function (event) {
		// 選択されたファイルを取得
		let selectedFile = event.target.files[0];

		// ファイルを読み込んでData URLに変換し、画像を表示
		let reader = new FileReader();
		reader.onload = function (e) {
			userPortrait.src = e.target.result;
		};
		reader.readAsDataURL(selectedFile);
	});

	let submitBtn = document.getElementById("profile-update");
	let loadingBtn = document.getElementById("loading-profile-update");

	let profileForm = document.getElementById("profile_form");
	profileForm.addEventListener("submit", function (e) {
		e.preventDefault();
		let formData = new FormData(profileForm);

		if (fileInput.files[0] != undefined) {
			let success = validateMedia(fileInput.files[0]);
			if (!success) return;
			formData.append("media", fileInput.files[0]);
		}

		switchButtonVisibility(submitBtn, loadingBtn);

		fetch("/form/profile/edit", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					window.location.href = `/profile?username=${data.newUsername}`;
				} else if (data.status === "error") {
					switchButtonVisibility(submitBtn, loadingBtn);

					alert(data.message);
				}
			})
			.catch((error) => {
				switchButtonVisibility(submitBtn, loadingBtn);

				alert("エラーが発生しました");
			});
	});

	function validateMedia(media) {
		const allowedImageTypes = [
			"image/png",
			"image/jpeg",
			"image/jpg",
			"image/webp",
		];
		let maxImageSize = 5 * 1024 * 1024;

		if (allowedImageTypes.includes(media.type) && maxImageSize > media.size) {
			return true;
		} else {
			alert("メディアはjpg, png, webp形式のみ対応しています");
			return false;
		}
	}
});
