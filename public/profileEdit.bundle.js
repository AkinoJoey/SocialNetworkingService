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

/***/ "./src/assets/js/profileEdit.js":
/*!**************************************!*\
  !*** ./src/assets/js/profileEdit.js ***!
  \**************************************/
/***/ (() => {

eval("document.addEventListener(\"DOMContentLoaded\", function () {\n\tlet fileInput = document.getElementById(\"file-input\");\n\tlet userPortrait = document.getElementById(\"user_portrait\");\n\n\t// 画像をクリックした時の処理\n\tuserPortrait.addEventListener(\"click\", function () {\n\t\t// file inputを起動\n\t\tfileInput.click();\n\t});\n\n\t// ファイルが選択された時の処理\n\tfileInput.addEventListener(\"change\", function (event) {\n\t\t// 選択されたファイルを取得\n\t\tlet selectedFile = event.target.files[0];\n\n\t\t// ファイルを読み込んでData URLに変換し、画像を表示\n\t\tlet reader = new FileReader();\n\t\treader.onload = function (e) {\n\t\t\tuserPortrait.src = e.target.result;\n\t\t};\n\t\treader.readAsDataURL(selectedFile);\n\t});\n\n\tlet profileForm = document.getElementById(\"profile_form\");\n\tprofileForm.addEventListener(\"submit\", function (e) {\n\t\te.preventDefault();\n\t\tlet formData = new FormData(profileForm);\n\n\t\tif (fileInput.files[0] != undefined) {\n\t\t\tformData.append(\"media\", fileInput.files[0]);\n\t\t}\n\n\t\tconsole.log(...formData.entries());\n\n\t\tfetch(\"/form/profile/edit\", {\n\t\t\tmethod: \"POST\",\n\t\t\tbody: formData,\n\t\t})\n\t\t\t.then((response) => response.json())\n\t\t\t.then((data) => {\n\t\t\t\tif (data.status === \"success\") {\n\t\t\t\t\twindow.location.href = `/profile?username=${data.newUsername}`;\n\t\t\t\t} else if (data.status === \"error\") {\n\t\t\t\t\talert(data.message);\n\t\t\t\t}\n\t\t\t})\n\t\t\t.catch((error) => {\n\t\t\t\talert(\"An error occurred. Please try again.\");\n\t\t\t});\n\t});\n});\n\n\n//# sourceURL=webpack:///./src/assets/js/profileEdit.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/assets/js/profileEdit.js"]();
/******/ 	
/******/ })()
;