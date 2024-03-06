import flatpickr from "flatpickr";
import { Japanese } from "flatpickr/dist/l10n/ja.js";

document.addEventListener("DOMContentLoaded", function () {
	const fileInputIcon = document.getElementById("file-input-icon");
	const createPostForm = document.getElementById("create-post-form");
	let fileInput = document.getElementById("file-input");
	let textInput = document.getElementById("content");
	let previewContainer = document.getElementById("previewContainer");
	let openPostElements = document.querySelectorAll(".open-post-element");

	openPostElements.forEach(function (openPostElement) {
		openPostElement.addEventListener("click", function () {
			setTimeout(() => {
				textInput.focus();
			}, 0);
		});
	});

	// 画像アイコンをクリックしたとき
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
						"hover:opacity-75",
						"absolute",
						"top-0",
						"right-0",
						"text-black",
						"bg-white",
					);
					removeBtn.innerHTML = `
				<i class="fas fa-times text-md"></i>
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

	// 新しい投稿をしたとき
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
					location.reload();
				} else if (data.status === "error") {
					alert(data.message);
				}
			})
			.catch((error) => {
				alert("An error occurred. Please try again.");
			});
	});

	textInput.addEventListener("input", function () {
		checkForm();
	});

	//　投稿フォームの画像が1文字以上もしくはメディアをアップロードしている時以外は投稿ボタンをdisabledにする
	function checkForm() {
		let submitBtn = document.getElementById("post-submit");

		if (textInput.value.length > 0 || fileInput.value) {
			submitBtn.classList.remove("btn", "btn-disabled");
		} else {
			submitBtn.classList.add("btn", "btn-disabled");
		}
	}

	// date
	const config = {
		enableTime: true,
		dateFormat: "Y-m-d H:i",
	};
	flatpickr(".flatpickr", config);
});
