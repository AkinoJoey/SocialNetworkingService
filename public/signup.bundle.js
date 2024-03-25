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

/***/ "./src/assets/js/changeLoadingBtn.js":
/*!*******************************************!*\
  !*** ./src/assets/js/changeLoadingBtn.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   switchButtonVisibility: () => (/* binding */ switchButtonVisibility)\n/* harmony export */ });\nfunction switchButtonVisibility(submitBtn, loadingBtn) {\r\n    submitBtn.classList.toggle('hidden');\r\n    loadingBtn.classList.toggle('hidden');\r\n}\r\n\r\n\n\n//# sourceURL=webpack:///./src/assets/js/changeLoadingBtn.js?");

/***/ }),

/***/ "./src/assets/js/signup.js":
/*!*********************************!*\
  !*** ./src/assets/js/signup.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _changeLoadingBtn__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./changeLoadingBtn */ \"./src/assets/js/changeLoadingBtn.js\");\n\r\n\r\ndocument.addEventListener(\"DOMContentLoaded\", function () {\r\n\tlet signupForm = document.getElementById(\"signup_form\");\r\n\tlet submitBtn = document.getElementById(\"submit_btn\");\r\n\tlet loadingBtn = document.getElementById(\"loading_btn\");\r\n\r\n\tsignupForm.addEventListener(\"submit\", function (e) {\r\n\t\te.preventDefault();\r\n\t\tlet formData = new FormData(signupForm);\r\n\t\tconsole.log(\"test\");\r\n\r\n\t\t(0,_changeLoadingBtn__WEBPACK_IMPORTED_MODULE_0__.switchButtonVisibility)(submitBtn, loadingBtn);\r\n\r\n\t\tfetch(\"/form/signup\", {\r\n\t\t\tmethod: \"POST\",\r\n\t\t\tbody: formData,\r\n\t\t})\r\n\t\t\t.then((response) => response.json())\r\n\t\t\t.then((data) => {\r\n\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\twindow.location.href = \"/login\";\r\n\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\t(0,_changeLoadingBtn__WEBPACK_IMPORTED_MODULE_0__.switchButtonVisibility)(submitBtn, loadingBtn);\r\n\r\n\t\t\t\t\talert(data.message);\r\n\t\t\t\t}\r\n\t\t\t})\r\n\t\t\t.catch((error) => {\r\n\t\t\t\t(0,_changeLoadingBtn__WEBPACK_IMPORTED_MODULE_0__.switchButtonVisibility)(submitBtn, loadingBtn);\r\n\r\n\t\t\t\talert(\"エラーが発生しました\");\r\n\t\t\t});\r\n\t});\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/signup.js?");

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
/******/ 	var __webpack_exports__ = __webpack_require__("./src/assets/js/signup.js");
/******/ 	
/******/ })()
;