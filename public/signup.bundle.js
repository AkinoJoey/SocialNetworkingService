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

/***/ "./src/assets/js/signup.js":
/*!*********************************!*\
  !*** ./src/assets/js/signup.js ***!
  \*********************************/
/***/ (() => {

eval("document.addEventListener(\"DOMContentLoaded\", function () {\r\n\tlet signupForm = document.getElementById(\"signup_form\");\r\n\r\n\tsignupForm.addEventListener(\"submit\", function (e) {\r\n\t\te.preventDefault();\r\n\t\tlet formData = new FormData(signupForm);\r\n\t\tconsole.log(\"test\");\r\n\r\n\t\tdocument.getElementById(\"submit_btn\").classList.add(\"hidden\");\r\n\t\tdocument.getElementById(\"loading_btn\").classList.remove(\"hidden\");\r\n\r\n\t\tfetch(\"/form/signup\", {\r\n\t\t\tmethod: \"POST\",\r\n\t\t\tbody: formData,\r\n\t\t})\r\n\t\t\t.then((response) => response.json())\r\n\t\t\t.then((data) => {\r\n\t\t\t\tif (data.status === 'success') {\r\n\t\t\t\t\t// バックエンド側でリダイレクト\r\n\t\t\t\t} else if (data.status === 'error') {\r\n\t\t\t\t\tdocument.getElementById(\"submit_btn\").classList.remove(\"hidden\");\r\n\t\t\t\t\tdocument.getElementById(\"loading_btn\").classList.add(\"hidden\");\r\n\t\t\t\t\talert(data.message);\r\n\t\t\t\t}\r\n\t\t\t})\r\n\t\t\t.catch(error => {\r\n\t\t\t\talert('エラーが発生しました');\r\n\t\t\t});\r\n\t});\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/signup.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/assets/js/signup.js"]();
/******/ 	
/******/ })()
;