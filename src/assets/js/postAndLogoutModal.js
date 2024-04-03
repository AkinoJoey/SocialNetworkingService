import flatpickr from "flatpickr";
import { Japanese } from "flatpickr/dist/l10n/ja.js";
import "flatpickr/dist/flatpickr.min.css";
import { switchButtonVisibility } from "./changeLoadingBtn";

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
		fileInput.click();
	});

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

	let today = new Date();

	// dateTime
	const config = {
		wrap: true,
		enableTime: true,
		dateFormat: "Y-m-d H:i",
		locale: Japanese,
		minDate: "today",
		maxDate: new Date(today.setMonth(today.getMonth() + 18)), // 最大18か月後
		minTime: new Date(today.getTime() + 5 * 60000), // 現在の時間から5分後の時間を計算
	};

	let fp = flatpickr(".flatpickr", config);
	let scheduledAtContainer = document.getElementById("scheduledAt_container");

	// 日付設定の決定するボタンを押した時
	document
		.getElementById("reserve_button")
		.addEventListener("click", function () {
			let selectedDates = fp.selectedDates;
			if (selectedDates.length > 0) {
				scheduledAtContainer.innerHTML = fp.formatDate(
					selectedDates[0],
					"Y-m-d H:i",
				);
			} else {
				scheduledAtContainer.innerHTML = "";
			}
		});

	let submitBtn = document.getElementById("post-submit");
	let loadingBtn = document.getElementById("post-loading-btn");

	// 新しい投稿をしたとき
	createPostForm.addEventListener("submit", function (e) {
		e.preventDefault();
		let formData = new FormData(createPostForm);

		if (fileInput.files[0] !== undefined) {
			let success = validateMedia(fileInput.files[0]);
			if (!success) return;
			formData.append("media", fileInput.files[0]);
		}


		if (scheduledAtContainer.textContent.trim() !== "") {
			let scheduledAt = scheduledAtContainer.textContent + ":00";
			formData.append("scheduled_at", scheduledAt);
		}

		switchButtonVisibility(submitBtn, loadingBtn);

		fetch("/form/new", {
			method: "POST",
			body: formData,
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.status === "success") {
					if (scheduledAtContainer.textContent.trim() !== "") {
						window.location.href = "/scheduled_posts";
					} else {
						location.reload();
					}
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

	textInput.addEventListener("input", function () {
		checkForm();
	});

	//　投稿フォームの画像が1文字以上もしくはメディアをアップロードしている時以外は投稿ボタンをdisabledにする
	function checkForm() {
		if (textInput.value.length > 0 || fileInput.value) {
			submitBtn.classList.remove("btn", "btn-disabled");
		} else {
			submitBtn.classList.add("btn", "btn-disabled");
		}
	}

	function validateMedia(media) {
		const allowedImageTypes = [
			"image/png",
			"image/gif",
			"image/jpeg",
			"image/jpg",
			"image/webp",
		];
		const allowedVideoTypes = ["video/mp4", "video/quicktime"];

		if (allowedImageTypes.includes(media.type)) {
			let maxImageSize = 5 * 1024 * 1024;

			if (maxImageSize < media.size) {
				alert("画像は5MB以内かつ、jpg, png, gif, webp形式のみ対応しています");
				return false;
			}

			return true;
		} else if (allowedVideoTypes.includes(media.type)) {
			let maxVideoSize = 40 * 1024 * 1024;

			if (maxVideoSize < media.size) {
				alert("動画は40MB以内かつ、mp4, movの拡張式のみ対応しています");
				return false;
			}

			return true;
		} else {
			alert("メディアはjpg, png, gif, webp, mp4, mov形式のみ対応しています");
			return false;
		}
	}
});

