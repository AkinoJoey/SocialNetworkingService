/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/assets/js/likeButton.js":
/*!*************************************!*\
  !*** ./src/assets/js/likeButton.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   deleteLikePost: () => (/* binding */ deleteLikePost),\n/* harmony export */   likePost: () => (/* binding */ likePost)\n/* harmony export */ });\nfunction likePost(\r\n\trequestUrl,\r\n\tformData,\r\n\tlikeBtn,\r\n\tnumberOfLikes,\r\n\tnumberOfLikesSpan,\r\n\tgoodIcon,\r\n) {\r\n\tfetch(requestUrl, {\r\n\t\tmethod: \"POST\",\r\n\t\tbody: formData,\r\n\t})\r\n\t\t.then((response) => response.json())\r\n\t\t.then((data) => {\r\n\t\t\tif (data.status === \"success\") {\r\n\t\t\t\tlikeBtn.setAttribute(\"data-isLike\", \"1\");\r\n\t\t\t\tnumberOfLikes += 1;\r\n\t\t\t\tnumberOfLikesSpan.innerHTML = numberOfLikes;\r\n\t\t\t\tgoodIcon.classList.add(\"fill-blue-600\");\r\n\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t// ユーザーにエラーメッセージを表示します\r\n\t\t\t\tconsole.error(data.message);\r\n\t\t\t\talert(\"Update failed: \" + data.message);\r\n\t\t\t}\r\n\t\t})\r\n\t\t.catch((error) => {\r\n\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t});\r\n}\r\n\r\nfunction deleteLikePost(\r\n\trequestUrl,\r\n\tformData,\r\n\tlikeBtn,\r\n\tnumberOfLikes,\r\n\tnumberOfLikesSpan,\r\n\tgoodIcon,\r\n) {\r\n\tfetch(requestUrl, {\r\n\t\tmethod: \"POST\",\r\n\t\tbody: formData,\r\n\t})\r\n\t\t.then((response) => response.json())\r\n\t\t.then((data) => {\r\n\t\t\tif (data.status === \"success\") {\r\n\t\t\t\tlikeBtn.setAttribute(\"data-isLike\", \"0\");\r\n\t\t\t\tnumberOfLikes -= 1;\r\n\t\t\t\tnumberOfLikesSpan.innerHTML = numberOfLikes;\r\n\t\t\t\tgoodIcon.classList.remove(\"fill-blue-600\");\r\n\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t// ユーザーにエラーメッセージを表示します\r\n\t\t\t\tconsole.error(data.message);\r\n\t\t\t\talert(\"Update failed: \" + data.message);\r\n\t\t\t}\r\n\t\t})\r\n\t\t.catch((error) => {\r\n\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t});\r\n}\r\n\r\n\n\n//# sourceURL=webpack:///./src/assets/js/likeButton.js?");

/***/ }),

/***/ "./src/assets/js/newPost.js":
/*!**********************************!*\
  !*** ./src/assets/js/newPost.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   checkForm: () => (/* binding */ checkForm),\n/* harmony export */   showFilePreview: () => (/* binding */ showFilePreview)\n/* harmony export */ });\n// ファイルプレビューを表示する関数\r\nfunction showFilePreview(event, previewContainer, textInput, fileInput) {\r\n\tlet file = event.target.files[0]; // 最初のファイルを取得\r\n\r\n\tif (file) {\r\n\t\tlet reader = new FileReader(); // ファイルリーダーオブジェクトを作成\r\n\r\n\t\treader.onload = function (e) {\r\n\t\t\tpreviewContainer.innerHTML = \"\"; // 以前のプレビューをクリア\r\n\t\t\tlet previewElement;\r\n\r\n\t\t\tif (file.type.startsWith(\"image/\")) {\r\n\t\t\t\t// 画像の場合\r\n\t\t\t\tpreviewElement = document.createElement(\"img\");\r\n\t\t\t} else if (file.type.startsWith(\"video/\")) {\r\n\t\t\t\t// 動画の場合\r\n\t\t\t\tpreviewElement = document.createElement(\"video\");\r\n\t\t\t\tpreviewElement.controls = true; // コントロールバーを表示する\r\n\t\t\t\tpreviewElement.autoplay = false; // 自動再生を無効にする\r\n\t\t\t\tpreviewElement.muted = true; // 音声をミュートにする（任意）\r\n\t\t\t}\r\n\r\n\t\t\tpreviewElement.src = e.target.result;\r\n\t\t\tpreviewElement.classList.add(\"w-full\");\r\n\r\n\t\t\tlet removeBtn = document.createElement(\"button\");\r\n\t\t\tremoveBtn.classList.add(\r\n\t\t\t\t\"rounded-full\",\r\n\t\t\t\t\"px-2\",\r\n\t\t\t\t\"hover:opacity-75\",\r\n\t\t\t\t\"absolute\",\r\n\t\t\t\t\"top-0\",\r\n\t\t\t\t\"right-0\",\r\n\t\t\t\t\"text-black\",\r\n\t\t\t\t\"bg-white\",\r\n\t\t\t);\r\n\t\t\tremoveBtn.innerHTML = `<i class=\"fas fa-times text-md\"></i>`;\r\n\t\t\tremoveBtn.addEventListener(\"click\", function () {\r\n\t\t\t\tpreviewContainer.removeChild(previewElement);\r\n\t\t\t\tpreviewContainer.removeChild(removeBtn);\r\n\t\t\t\tfileInput.value = \"\";\r\n\t\t\t\tcheckForm(textInput, fileInput);\r\n\t\t\t});\r\n\r\n\t\t\tpreviewContainer.appendChild(previewElement);\r\n\t\t\tpreviewContainer.appendChild(removeBtn);\r\n\t\t\tcheckForm(textInput, fileInput);\r\n\t\t};\r\n\r\n\t\treader.readAsDataURL(file); // ファイルの内容をBase64エンコードして読み込む\r\n\t}\r\n}\r\n\r\n// フォームをチェックして送信ボタンのスタイルを変更する関数\r\nfunction checkForm(textInput, fileInput, submitBtn) {\r\n\tif (textInput.value.length > 0 || fileInput.value) {\r\n\t\tsubmitBtn.classList.remove(\"btn\", \"btn-disabled\");\r\n\t} else {\r\n\t\tsubmitBtn.classList.add(\"btn\", \"btn-disabled\");\r\n\t}\r\n}\r\n\r\n \r\n\n\n//# sourceURL=webpack:///./src/assets/js/newPost.js?");

/***/ }),

/***/ "./src/assets/js/posts.js":
/*!********************************!*\
  !*** ./src/assets/js/posts.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _likeButton__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./likeButton */ \"./src/assets/js/likeButton.js\");\n/* harmony import */ var _newPost__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./newPost */ \"./src/assets/js/newPost.js\");\n\r\n\r\n\r\ndocument.addEventListener(\"DOMContentLoaded\", function () {\r\n\tlet likeButtons = document.querySelectorAll(\".like-btn\");\r\n\tlet deleteButtons = document.querySelectorAll(\".delete-btn\");\r\n\tlet path = location.pathname;\r\n\r\n\tlikeButtons.forEach(function (likeBtn) {\r\n\t\tlikeBtn.addEventListener(\"click\", function (e) {\r\n\t\t\te.preventDefault();\r\n\r\n\t\t\tlet numberOfLikesSpan = likeBtn.querySelector(\".number-of-likes\");\r\n\t\t\tlet numberOfLikes = Number(numberOfLikesSpan.textContent);\r\n\t\t\tlet goodIcon = likeBtn.querySelector(\".good-icon\");\r\n\t\t\tlet isLike = likeBtn.getAttribute(\"data-isLike\");\r\n\r\n\t\t\tlet formData = new FormData();\r\n\t\t\tlet postId = likeBtn.getAttribute(\"data-post-id\");\r\n\t\t\tformData.append(\"csrf_token\", csrfToken);\r\n\t\t\tformData.append(\"post_id\", postId);\r\n\r\n\t\t\tlet requestUrl = \"\";\r\n\r\n\t\t\tif (isLike === \"1\") {\r\n\t\t\t\tif (likeBtn.name === \"post_like_btn\" && path === \"/posts\") {\r\n\t\t\t\t\trequestUrl = \"form/delete-like-post\";\r\n\t\t\t\t} else {\r\n\t\t\t\t\trequestUrl = \"form/delete-like-comment\";\r\n\t\t\t\t}\r\n\r\n\t\t\t\t(0,_likeButton__WEBPACK_IMPORTED_MODULE_0__.deleteLikePost)(\r\n\t\t\t\t\trequestUrl,\r\n\t\t\t\t\tformData,\r\n\t\t\t\t\tlikeBtn,\r\n\t\t\t\t\tnumberOfLikes,\r\n\t\t\t\t\tnumberOfLikesSpan,\r\n\t\t\t\t\tgoodIcon,\r\n\t\t\t\t);\r\n\t\t\t} else {\r\n\t\t\t\tif (likeBtn.name === \"post_like_btn\" && path === \"/posts\") {\r\n\t\t\t\t\trequestUrl = \"form/like-post\";\r\n\t\t\t\t} else {\r\n\t\t\t\t\trequestUrl = \"form/like-comment\";\r\n\t\t\t\t}\r\n\r\n\t\t\t\t(0,_likeButton__WEBPACK_IMPORTED_MODULE_0__.likePost)(\r\n\t\t\t\t\trequestUrl,\r\n\t\t\t\t\tformData,\r\n\t\t\t\t\tlikeBtn,\r\n\t\t\t\t\tnumberOfLikes,\r\n\t\t\t\t\tnumberOfLikesSpan,\r\n\t\t\t\t\tgoodIcon,\r\n\t\t\t\t);\r\n\t\t\t}\r\n\t\t});\r\n\t});\r\n\r\n\tconst alertModalEl = document.getElementById(\"alert-modal\");\r\n\tconst alertModal = new Modal(alertModalEl);\r\n\tconst deleteExecuteBtn = document.getElementById(\"delete-execute-btn\");\r\n\r\n\tdeleteButtons.forEach(function (deleteBtn) {\r\n\t\tdeleteBtn.addEventListener(\"click\", function (e) {\r\n\t\t\te.preventDefault();\r\n\t\t\talertModal.show();\r\n\r\n\t\t\tdeleteExecuteBtn.addEventListener(\"click\", function () {\r\n\t\t\t\tlet formData = new FormData();\r\n\t\t\t\tlet postId = deleteBtn.getAttribute(\"data-post-id\");\r\n\t\t\t\tformData.append(\"csrf_token\", csrfToken);\r\n\r\n\t\t\t\tif (deleteBtn.name === \"delete_post_btn\" && path === \"/posts\") {\r\n\t\t\t\t\tformData.append(\"post_id\", postId);\r\n\t\t\t\t\tfetch(\"delete/post\", {\r\n\t\t\t\t\t\tmethod: \"POST\",\r\n\t\t\t\t\t\tbody: formData,\r\n\t\t\t\t\t})\r\n\t\t\t\t\t\t.then((response) => response.json())\r\n\t\t\t\t\t\t.then((data) => {\r\n\t\t\t\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\t\t\t\twindow.location.href = \"/\";\r\n\t\t\t\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\t\t\t\tconsole.error(data.message);\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t})\r\n\t\t\t\t\t\t.catch((error) => {\r\n\t\t\t\t\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t\t\t\t\t});\r\n\t\t\t\t} else if (\r\n\t\t\t\t\tdeleteBtn.name === \"delete_post_btn\" &&\r\n\t\t\t\t\tpath === \"/comments\"\r\n\t\t\t\t) {\r\n\t\t\t\t\tformData.append(\"comment_id\", postId);\r\n\t\t\t\t\tfetch(\"delete/comment\", {\r\n\t\t\t\t\t\tmethod: \"POST\",\r\n\t\t\t\t\t\tbody: formData,\r\n\t\t\t\t\t})\r\n\t\t\t\t\t\t.then((response) => response.json())\r\n\t\t\t\t\t\t.then((data) => {\r\n\t\t\t\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\t\t\t\twindow.location.href = \"/\";\r\n\t\t\t\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\t\t\t\tconsole.error(data.message);\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t})\r\n\t\t\t\t\t\t.catch((error) => {\r\n\t\t\t\t\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t\t\t\t\t});\r\n\t\t\t\t} else {\r\n\t\t\t\t\tformData.append(\"comment_id\", postId);\r\n\t\t\t\t\tfetch(\"delete/comment\", {\r\n\t\t\t\t\t\tmethod: \"POST\",\r\n\t\t\t\t\t\tbody: formData,\r\n\t\t\t\t\t})\r\n\t\t\t\t\t\t.then((response) => response.json())\r\n\t\t\t\t\t\t.then((data) => {\r\n\t\t\t\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\t\t\t\tlocation.reload();\r\n\t\t\t\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\t\t\t\tconsole.error(data.message);\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t})\r\n\t\t\t\t\t\t.catch((error) => {\r\n\t\t\t\t\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t\t\t\t\t});\r\n\t\t\t\t}\r\n\t\t\t});\r\n\t\t});\r\n\t});\r\n\r\n\r\n\tconst fileInputIcon = document.getElementById(\"reply-file-input-icon\");\r\n\tlet fileInput = document.getElementById(\"reply-file-input\");\r\n\tlet previewContainer = document.getElementById(\"reply-previewContainer\");\r\n\tlet textInput = document.getElementById(\"reply-content\");\r\n\tlet submitBtn = document.getElementById('reply-submit');\r\n\r\n\t// 画像アイコンをクリックしたとき\r\n\tfileInputIcon.addEventListener(\"click\", function () {\r\n\t\tfileInput.addEventListener(\"change\", function (event) {\r\n\t\t\t(0,_newPost__WEBPACK_IMPORTED_MODULE_1__.showFilePreview)(event, previewContainer, textInput, fileInput);\r\n\r\n\t\t\t(0,_newPost__WEBPACK_IMPORTED_MODULE_1__.checkForm)(textInput, fileInput, submitBtn);\r\n\t\t})\r\n\r\n\t\tfileInput.click();\r\n\t});\r\n\r\n\ttextInput.addEventListener(\"input\", function () {\r\n\t\t(0,_newPost__WEBPACK_IMPORTED_MODULE_1__.checkForm)(textInput, fileInput, submitBtn);\r\n\t});\r\n\r\n\tlet replyForm = document.getElementById(\"reply-form\");\r\n\r\n\t// リプライを送信したとき\r\n\treplyForm.addEventListener(\"submit\", function (e) {\r\n\t\te.preventDefault();\r\n\t\tlet formData = new FormData(replyForm);\r\n\t\tlet type = location.pathname === \"/posts\" ? \"post\" : \"comment\";\r\n\t\tformData.append(\"type_reply_to\", type);\r\n\t\tformData.append('media', fileInput.files[0]);\r\n\r\n\t\tfetch(\"/form/reply\", {\r\n\t\t\tmethod: \"POST\",\r\n\t\t\tbody: formData,\r\n\t\t})\r\n\t\t\t.then((response) => response.json())\r\n\t\t\t.then((data) => {\r\n\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\tlocation.reload();\r\n\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\talert(data.message);\r\n\t\t\t\t}\r\n\t\t\t})\r\n\t\t\t.catch((error) => {\r\n\t\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t\t});\r\n\t});\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/posts.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./src/assets/js/posts.js");
/******/ 	
/******/ })()
;