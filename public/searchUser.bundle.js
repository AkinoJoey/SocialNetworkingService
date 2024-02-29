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

eval("document.addEventListener(\"DOMContentLoaded\", function () {\r\n\tlet searchForm = document.getElementById(\"search-form\");\r\n\tconst inputKeyword = document.getElementById(\"keyword\");\r\n\tinputKeyword.focus();\r\n\r\n\tinputKeyword.addEventListener(\"keydown\", function (e) {\r\n\t\tif (e.key === \"Enter\") {\r\n\t\t\te.preventDefault();\r\n\t\t\tsearch();\r\n\t\t}\r\n\t});\r\n\r\n\tsearchForm.addEventListener(\"submit\", function (e) {\r\n\t\te.preventDefault();\r\n\t\tsearch();\r\n\t});\r\n\r\n\tfunction search() {\r\n\t\tconst formData = new FormData(searchForm);\r\n\t\twindow.location.href = `/search/user?keyword=${formData.get(\"keyword\")}`;\r\n\t}\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/searchUser.js?");

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