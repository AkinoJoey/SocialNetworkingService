// ファイルプレビューを表示する関数
function showFilePreview(event, previewContainer, textInput, fileInput) {
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
			removeBtn.innerHTML = `<i class="fas fa-times text-md"></i>`;
			removeBtn.addEventListener("click", function () {
				previewContainer.removeChild(previewElement);
				previewContainer.removeChild(removeBtn);
				fileInput.value = "";
				checkForm(textInput, fileInput);
			});

			previewContainer.appendChild(previewElement);
			previewContainer.appendChild(removeBtn);
			checkForm(textInput, fileInput);
		};

		reader.readAsDataURL(file); // ファイルの内容をBase64エンコードして読み込む
	}
}

// フォームをチェックして送信ボタンのスタイルを変更する関数
function checkForm(textInput, fileInput, submitBtn) {
	if (textInput.value.length > 0 || fileInput.value) {
		submitBtn.classList.remove("btn", "btn-disabled");
	} else {
		submitBtn.classList.add("btn", "btn-disabled");
	}
}

export { showFilePreview, checkForm }; 
