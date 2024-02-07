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

/***/ "./src/assets/js/posts.js":
/*!********************************!*\
  !*** ./src/assets/js/posts.js ***!
  \********************************/
/***/ (() => {

eval("document.addEventListener(\"DOMContentLoaded\", function () {\r\n    const likePostForm = document.getElementById(\"like_post_form\");\r\n\tlet like = false;\r\n\tlet numberOfPostLikeSpan = document.getElementById(\"number_of_post_like\");\r\n\tlet numberOfPostLike = Number(numberOfPostLikeSpan.textContent);\r\n\r\n\tlikePostForm.addEventListener(\"submit\", function (event) {\r\n\t\tevent.preventDefault();\r\n\r\n\t\tconst formData = new FormData(form);\r\n\r\n\t\tif (like) {\r\n\t\t\tdeleteLikePost(formData);\r\n\r\n\t\t} else {\r\n\t\t\tlikePost(formData);\r\n\t\t}\r\n\t});\r\n\r\n\tfunction likePost(formData) {\r\n\t\tfetch(\"form/like-post\", {\r\n\t\t\tmethod: \"POST\",\r\n\t\t\tbody: formData,\r\n\t\t})\r\n\t\t\t.then((response) => response.json()) \r\n\t\t\t.then((data) => {\r\n\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\tlike = true;\r\n\t\t\t\t\tnumberOfPostLike += 1;\r\n\t\t\t\t\tnumberOfPostLikeSpan.innerHTML = numberOfPostLike;\r\n\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\t// ユーザーにエラーメッセージを表示します\r\n\t\t\t\t\tconsole.error(data.message);\r\n\t\t\t\t\talert(\"Update failed: \" + data.message);\r\n\t\t\t\t}\r\n\t\t\t})\r\n\t\t\t.catch((error) => {\r\n\t\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t\t});\r\n\t}\r\n\r\n\tfunction deleteLikePost(formData) {\r\n\t\tfetch(\"form/like-post\", {\r\n\t\t\tmethod: \"POST\",\r\n\t\t\tbody: formData,\r\n\t\t})\r\n\t\t\t.then((response) => response.json())\r\n\t\t\t.then((data) => {\r\n\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\tlike = false;\r\n\t\t\t\t\tnumberOfPostLike -= 1;\r\n\t\t\t\t\tnumberOfPostLikeSpan.innerHTML = numberOfPostLike;\r\n\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\t// ユーザーにエラーメッセージを表示します\r\n\t\t\t\t\tconsole.error(data.message);\r\n\t\t\t\t\talert(\"Update failed: \" + data.message);\r\n\t\t\t\t}\r\n\t\t\t})\r\n\t\t\t.catch((error) => {\r\n\t\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t\t});\r\n\t}\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/posts.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/assets/js/posts.js"]();
/******/ 	
/******/ })()
;