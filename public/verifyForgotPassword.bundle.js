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

/***/ "./src/assets/js/verifyForgotPassword.js":
/*!***********************************************!*\
  !*** ./src/assets/js/verifyForgotPassword.js ***!
  \***********************************************/
/***/ (() => {

eval("document.addEventListener(\"DOMContentLoaded\", function () {\n\tlet passwordForm = document.getElementById(\"password_form\");\n\n\tpasswordForm.addEventListener(\"submit\", function (e) {\n\t\te.preventDefault();\n\t\tlet formData = new FormData(passwordForm);\n\n\t\tfetch(\"/form/verify/forgot_password\", {\n\t\t\tmethod: \"POST\",\n\t\t\tbody: formData,\n\t\t})\n\t\t\t.then((response) => response.json())\n\t\t\t.then((data) => {\n\t\t\t\tif (data.status === \"success\") {\n\t\t\t\t\twindow.location.href = \"/login\";\n\t\t\t\t} else if (data.status === \"error\") {\n\t\t\t\t\talert(data.message);\n\t\t\t\t}\n\t\t\t})\n\t\t\t.catch((error) => {\n\t\t\t\talert(\"An error occurred. Please try again.\");\n\t\t\t});\n\t});\n});\n\n\n//# sourceURL=webpack:///./src/assets/js/verifyForgotPassword.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/assets/js/verifyForgotPassword.js"]();
/******/ 	
/******/ })()
;