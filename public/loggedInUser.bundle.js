/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/assets/js/postAndLogoutModal.js":
/*!*********************************************!*\
  !*** ./src/assets/js/postAndLogoutModal.js ***!
  \*********************************************/
/***/ (() => {

eval("document.addEventListener(\"DOMContentLoaded\", function () {\r\n\tconst fileInputIcon = document.getElementById(\"file-input-icon\");\r\n\tconst createPostForm = document.getElementById(\"create-post-form\");\r\n\tlet fileInput = document.getElementById(\"file-input\");\r\n\tlet textInput = document.getElementById(\"content\");\r\n\tlet previewContainer = document.getElementById(\"previewContainer\");\r\n\tlet openPostElements = document.querySelectorAll(\".open-post-element\");\r\n\r\n\topenPostElements.forEach(function (openPostElement) {\r\n\t\topenPostElement.addEventListener(\"click\", function () {\r\n\t\t\tsetTimeout(() => {\r\n\t\t\t\ttextInput.focus();\r\n\t\t\t}, 0);\r\n\t\t});\r\n\t});\r\n\r\n\t// 画像アイコンをクリックしたとき\r\n\tfileInputIcon.addEventListener(\"click\", function () {\r\n\t\tfileInput.addEventListener(\"change\", function (event) {\r\n\t\t\tlet file = event.target.files[0]; // 最初のファイルを取得\r\n\r\n\t\t\tif (file) {\r\n\t\t\t\tlet reader = new FileReader(); // ファイルリーダーオブジェクトを作成\r\n\r\n\t\t\t\treader.onload = function (e) {\r\n\t\t\t\t\tpreviewContainer.innerHTML = \"\"; // 以前のプレビューをクリア\r\n\t\t\t\t\tlet previewElement;\r\n\r\n\t\t\t\t\tif (file.type.startsWith(\"image/\")) {\r\n\t\t\t\t\t\t// 画像の場合\r\n\t\t\t\t\t\tpreviewElement = document.createElement(\"img\");\r\n\t\t\t\t\t} else if (file.type.startsWith(\"video/\")) {\r\n\t\t\t\t\t\t// 動画の場合\r\n\t\t\t\t\t\tpreviewElement = document.createElement(\"video\");\r\n\t\t\t\t\t\tpreviewElement.controls = true; // コントロールバーを表示する\r\n\t\t\t\t\t\tpreviewElement.autoplay = false; // 自動再生を無効にする\r\n\t\t\t\t\t\tpreviewElement.muted = true; // 音声をミュートにする（任意）\r\n\t\t\t\t\t}\r\n\r\n\t\t\t\t\tpreviewElement.src = e.target.result;\r\n\t\t\t\t\tpreviewElement.classList.add(\"w-full\");\r\n\r\n\t\t\t\t\tlet removeBtn = document.createElement(\"button\");\r\n\t\t\t\t\tremoveBtn.classList.add(\r\n\t\t\t\t\t\t\"rounded-full\",\r\n\t\t\t\t\t\t\"px-2\",\r\n\t\t\t\t\t\t\"hover:opacity-75\",\r\n\t\t\t\t\t\t\"absolute\",\r\n\t\t\t\t\t\t\"top-0\",\r\n\t\t\t\t\t\t\"right-0\",\r\n\t\t\t\t\t\t\"text-black\",\r\n\t\t\t\t\t\t\"bg-white\",\r\n\t\t\t\t\t);\r\n\t\t\t\t\tremoveBtn.innerHTML = `\r\n\t\t\t\t<i class=\"fas fa-times text-md\"></i>\r\n\t\t\t\t`;\r\n\t\t\t\t\tremoveBtn.addEventListener(\"click\", function () {\r\n\t\t\t\t\t\tpreviewContainer.removeChild(previewElement);\r\n\t\t\t\t\t\tpreviewContainer.removeChild(removeBtn);\r\n\t\t\t\t\t\tfileInput.value = \"\";\r\n\t\t\t\t\t\tcheckForm();\r\n\t\t\t\t\t});\r\n\r\n\t\t\t\t\tpreviewContainer.appendChild(previewElement);\r\n\t\t\t\t\tpreviewContainer.appendChild(removeBtn);\r\n\t\t\t\t\tcheckForm();\r\n\t\t\t\t};\r\n\r\n\t\t\t\treader.readAsDataURL(file); // ファイルの内容をBase64エンコードして読み込む\r\n\t\t\t}\r\n\t\t\tcheckForm();\r\n\t\t});\r\n\r\n\t\tfileInput.click();\r\n\t});\r\n\r\n\t// 新しい投稿をしたとき\r\n\tcreatePostForm.addEventListener(\"submit\", function (e) {\r\n\t\te.preventDefault();\r\n\t\tlet formData = new FormData(createPostForm);\r\n\t\tformData.append(\"media\", fileInput.files[0]);\r\n\r\n\t\tfetch(\"/form/new\", {\r\n\t\t\tmethod: \"POST\",\r\n\t\t\tbody: formData,\r\n\t\t})\r\n\t\t\t.then((response) => response.json())\r\n\t\t\t.then((data) => {\r\n\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\tconsole.log(\"success\");\r\n\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\talert(data.message);\r\n\t\t\t\t}\r\n\t\t\t})\r\n\t\t\t.catch((error) => {\r\n\t\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t\t});\r\n\t});\r\n\r\n\ttextInput.addEventListener(\"input\", function () {\r\n\t\tcheckForm();\r\n\t});\r\n\r\n\t//　投稿フォームの画像が1文字以上もしくはメディアをアップロードしている時以外は投稿ボタンをdisabledにする\r\n\tfunction checkForm() {\r\n\t\tlet submitBtn = document.getElementById(\"post-submit\");\r\n\r\n\t\tif (textInput.value.length > 0 || fileInput.value) {\r\n\t\t\tsubmitBtn.classList.remove(\"btn\", \"btn-disabled\");\r\n\t\t} else {\r\n\t\t\tsubmitBtn.classList.add(\"btn\", \"btn-disabled\");\r\n\t\t}\r\n\t}\r\n\r\n\t//　ログアウト用のモーダル\r\n\tconst logoutModalEl = document.getElementById(\"logout-modal\");\r\n\tconst logoutModal = new Modal(logoutModalEl);\r\n\tconst logoutButtons = document.querySelectorAll(\".logout-btn\");\r\n\r\n\tlogoutButtons.forEach(function (logoutBtn) {\r\n\t\tlogoutBtn.addEventListener(\"click\", function (e) {\r\n\t\t\te.preventDefault();\r\n\t\t\tlogoutModal.show();\r\n\t\t});\r\n\t});\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/postAndLogoutModal.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/assets/js/postAndLogoutModal.js"]();
/******/ 	
/******/ })()
;