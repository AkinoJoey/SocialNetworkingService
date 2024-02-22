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

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   deleteLikePost: () => (/* binding */ deleteLikePost),\n/* harmony export */   likePost: () => (/* binding */ likePost)\n/* harmony export */ });\nfunction likePost(\r\n\tformData,\r\n\tlikeBtn,\r\n\tnumberOfPostLike,\r\n\tnumberOfPostLikeSpan,\r\n\tgoodBtn,\r\n) {\r\n\tfetch(\"/form/like-post\", {\r\n\t\tmethod: \"POST\",\r\n\t\tbody: formData,\r\n\t})\r\n\t\t.then((response) => response.json())\r\n\t\t.then((data) => {\r\n\t\t\tif (data.status === \"success\") {\r\n\t\t\t\tlikeBtn.setAttribute(\"data-isLike\", \"1\");\r\n\t\t\t\tnumberOfPostLike += 1;\r\n\t\t\t\tnumberOfPostLikeSpan.innerHTML = numberOfPostLike;\r\n\t\t\t\tgoodBtn.classList.add(\"fill-blue-600\");\r\n\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t// ユーザーにエラーメッセージを表示します\r\n\t\t\t\tconsole.error(data.message);\r\n\t\t\t\talert(\"Update failed: \" + data.message);\r\n\t\t\t}\r\n\t\t})\r\n\t\t.catch((error) => {\r\n\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t});\r\n}\r\n\r\nfunction deleteLikePost(\r\n\tformData,\r\n\tlikeBtn,\r\n\tnumberOfPostLike,\r\n\tnumberOfPostLikeSpan,\r\n\tgoodBtn,\r\n) {\r\n\tfetch(\"/form/delete-like-post\", {\r\n\t\tmethod: \"POST\",\r\n\t\tbody: formData,\r\n\t})\r\n\t\t.then((response) => response.json())\r\n\t\t.then((data) => {\r\n\t\t\tif (data.status === \"success\") {\r\n\t\t\t\tlikeBtn.setAttribute(\"data-isLike\", \"0\");\r\n\t\t\t\tnumberOfPostLike -= 1;\r\n\t\t\t\tnumberOfPostLikeSpan.innerHTML = numberOfPostLike;\r\n\t\t\t\tgoodBtn.classList.remove(\"fill-blue-600\");\r\n\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t// ユーザーにエラーメッセージを表示します\r\n\t\t\t\tconsole.error(data.message);\r\n\t\t\t\talert(\"Update failed: \" + data.message);\r\n\t\t\t}\r\n\t\t})\r\n\t\t.catch((error) => {\r\n\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t});\r\n}\r\n\n\n//# sourceURL=webpack:///./src/assets/js/likeButton.js?");

/***/ }),

/***/ "./src/assets/js/top.js":
/*!******************************!*\
  !*** ./src/assets/js/top.js ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _likeButton__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./likeButton */ \"./src/assets/js/likeButton.js\");\n\r\n\r\ndocument.addEventListener(\"DOMContentLoaded\", function () {\r\n\tlet likeButtons = document.querySelectorAll(\".like-btn\");\r\n\r\n\tlikeButtons.forEach(function (likeBtn) {\r\n\t\tlikeBtn.addEventListener(\"click\", function (e) {\r\n\t\t\te.preventDefault();\r\n\t\t\tlet numberOfPostLikeSpan = likeBtn.querySelector(\".number-of-post-likes\");\r\n\t\t\tlet numberOfPostLike = Number(numberOfPostLikeSpan.textContent);\r\n\t\t\tlet goodBtn = likeBtn.querySelector(\".good-btn\");\r\n\r\n\t\t\tlet isLike = likeBtn.getAttribute(\"data-isLike\");\r\n\t\t\tlet postId = likeBtn.getAttribute(\"data-post-id\");\r\n\r\n\t\t\tlet formData = new FormData();\r\n\t\t\tformData.append(\"post_id\", postId);\r\n\t\t\tformData.append(\"csrf_token\", csrfToken);\r\n\r\n\t\t\tif (isLike === \"1\") {\r\n\t\t\t\t(0,_likeButton__WEBPACK_IMPORTED_MODULE_0__.deleteLikePost)(\r\n\t\t\t\t\tformData,\r\n\t\t\t\t\tlikeBtn,\r\n\t\t\t\t\tnumberOfPostLike,\r\n\t\t\t\t\tnumberOfPostLikeSpan,\r\n\t\t\t\t\tgoodBtn,\r\n\t\t\t\t);\r\n\t\t\t} else {\r\n\t\t\t\t(0,_likeButton__WEBPACK_IMPORTED_MODULE_0__.likePost)(\r\n\t\t\t\t\tformData,\r\n\t\t\t\t\tlikeBtn,\r\n\t\t\t\t\tnumberOfPostLike,\r\n\t\t\t\t\tnumberOfPostLikeSpan,\r\n\t\t\t\t\tgoodBtn,\r\n\t\t\t\t);\r\n\t\t\t}\r\n\t\t});\r\n\t});\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/top.js?");

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
/******/ 	var __webpack_exports__ = __webpack_require__("./src/assets/js/top.js");
/******/ 	
/******/ })()
;