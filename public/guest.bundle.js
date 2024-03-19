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

/***/ "./src/assets/js/guest.js":
/*!********************************!*\
  !*** ./src/assets/js/guest.js ***!
  \********************************/
/***/ (() => {

eval("document.addEventListener(\"DOMContentLoaded\", async function () {\r\n\tlet offsetCounter = 0;\r\n    let postsContainer = document.getElementById(\"posts_container\");\r\n    \r\n    await fetchPost();\r\n\r\n\tasync function fetchPost() {\r\n\t\ttry {\r\n\t\t\tconst response = await fetch(\r\n\t\t\t\t`/timeline/guest?offset=${offsetCounter}`,\r\n\t\t\t\t{\r\n\t\t\t\t\tmethod: \"GET\",\r\n\t\t\t\t},\r\n\t\t\t);\r\n\r\n\t\t\tconst data = await response.json();\r\n\r\n\t\t\tif (data.status === \"success\") {\r\n\t\t\t\tlet newPosts = document.createElement(\"div\");\r\n\t\t\t\tnewPosts.innerHTML = data.htmlString;\r\n\r\n\t\t\t\tpostsContainer.appendChild(newPosts);\r\n\r\n\t\t\t\t// offsetを更新\r\n\t\t\t\t// TODO: 値を20にする\r\n\t\t\t\toffsetCounter += 3;\r\n\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\tconsole.error(data.message);\r\n\t\t\t}\r\n\t\t} catch (error) {\r\n\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t}\r\n\t}\r\n\r\n\t// 無限スクロール\r\n\twindow.addEventListener(\"scroll\", function () {\r\n\t\tlet documentHeight = document.documentElement.scrollHeight;\r\n\r\n\t\t// 現在のスクロール位置\r\n\t\tlet scrollTop = window.scrollY || document.documentElement.scrollTop;\r\n\r\n\t\tlet windowHeight = window.innerHeight;\r\n\r\n\t\t// スクロール位置が最下部に近づいているかどうかをチェック\r\n\t\tif (documentHeight - scrollTop <= windowHeight) {\r\n\t\t\tfetchPost();\r\n\t\t}\r\n\t});\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/guest.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/assets/js/guest.js"]();
/******/ 	
/******/ })()
;