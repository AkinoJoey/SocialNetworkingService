import "flowbite";
import "../css/main.css";

document.addEventListener("DOMContentLoaded", function () {
	const fileInputIcon = document.getElementById("file-input-icon");
	const createPostForm = document.getElementById("create-post-form");
	let fileInput = document.getElementById("file-input");
	let textInput = document.getElementById("content");
	let previewContainer = document.getElementById("previewContainer");

	document
		.getElementById("open_post_modal")
		.addEventListener("click", function () {
			setTimeout(() => {
				textInput.focus();
			}, 0);
		});

	fileInputIcon.addEventListener("click", function () {
		fileInput.addEventListener("change", function (event) {
			let file = event.target.files[0]; // 最初のファイルを取得

			if (file) {
				let reader = new FileReader(); // ファイルリーダーオブジェクトを作成

				reader.onload = function (e) {
					previewContainer.innerHTML = ""; // 以前のプレビューをクリア
					let previewElement;

					if (file.type.startsWith("image/")) {
						// 画像の場合
						previewElement = document.createElement("img");
					} else if (file.type.startsWith("video/")) {
						// 動画の場合
						previewElement = document.createElement("video");
						previewElement.controls = true; // コントロールバーを表示する
						previewElement.autoplay = false; // 自動再生を無効にする
						previewElement.muted = true; // 音声をミュートにする（任意）
					}

					previewElement.src = e.target.result;
					previewElement.classList.add("w-full");

					let removeBtn = document.createElement("button");
					removeBtn.classList.add(
						"rounded-full",
						"px-2",
						"hover:opacity-50",
						"absolute",
						"top-0",
						"right-0",
						"text-white",
					);
					removeBtn.innerHTML = `
				<i class="fas fa-times text-xl"></i>
				`;
					removeBtn.addEventListener("click", function () {
						previewContainer.removeChild(previewElement);
						previewContainer.removeChild(removeBtn);
						fileInput.value = "";
						checkForm();
					});

					previewContainer.appendChild(previewElement);
					previewContainer.appendChild(removeBtn);
					checkForm();
				};

				reader.readAsDataURL(file); // ファイルの内容をBase64エンコードして読み込む
			}
			checkForm();
		});

		fileInput.click();
	});

	createPostForm.addEventListener("submit", function (e) {
		e.preventDefault();
		let formData = new FormData(createPostForm);
		formData.append("media", fileInput.files[0]);

		fetch("/form/new", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					console.log("success");
				} else if (data.status === "error") {
					// ユーザーにエラーメッセージを表示します
					console.error(data.message);
				}
			})
			.catch((error) => {
				alert("An error occurred. Please try again.");
			});
	});

	textInput.addEventListener("input", function () {
		checkForm();
	});

	function checkForm() {
		let submitBtn = document.getElementById("post-submit");

		if (textInput.value.length > 0 || fileInput.value) {
			submitBtn.classList.remove("btn", "btn-disabled");
		} else {
			submitBtn.classList.add("btn", "btn-disabled");
		}
	}
});
