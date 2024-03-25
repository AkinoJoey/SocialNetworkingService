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

/***/ "./src/assets/js/searchUser.js":
/*!*************************************!*\
  !*** ./src/assets/js/searchUser.js ***!
  \*************************************/
/***/ (() => {

eval("document.addEventListener(\"DOMContentLoaded\", function () {\r\n\tconst inputKeyword = document.getElementById(\"keyword\");\r\n\tinputKeyword.focus();\r\n\tsearch(\"\");\r\n\r\n\tlet searchDeleteBtn = document.getElementById(\"search_delete\");\r\n\tsearchDeleteBtn.addEventListener(\"click\", function (e) {\r\n\t\tinputKeyword.value = \"\";\r\n\t\tsearch(\"\");\r\n\t});\r\n\r\n\tinputKeyword.addEventListener(\"input\", function (e) {\r\n\t\tlet keyword = e.target.value;\r\n\t\tsearch(keyword);\r\n\t});\r\n\r\n\tlet usersContainer = document.getElementById(\"users_container\");\r\n\r\n\tfunction search(keyword) {\r\n\t\tfetch(`/search/user-list?keyword=${keyword}`, {\r\n\t\t\tmethod: \"GET\",\r\n\t\t})\r\n\t\t\t.then((response) => response.json())\r\n\t\t\t.then((data) => {\r\n\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\tusersContainer.innerHTML = data.htmlString;\r\n\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\talert(data.message);\r\n\t\t\t\t}\r\n\t\t\t})\r\n\t\t\t.catch((error) => {\r\n\t\t\t\talert(\"エラーが発生しました。更新してみてください\");\r\n\t\t\t});\r\n\t}\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/searchUser.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/assets/js/searchUser.js"]();
/******/ 	
/******/ })()
;