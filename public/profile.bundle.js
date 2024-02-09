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

/***/ "./src/assets/js/profile.js":
/*!**********************************!*\
  !*** ./src/assets/js/profile.js ***!
  \**********************************/
/***/ (() => {

eval("document.addEventListener(\"DOMContentLoaded\", function () {\r\n\tconst followForm = document.getElementById(\"follow_form\");\r\n    const followerCount = document.getElementById('followerCount');\r\n    let numberOfFollower = Number(followerCount.textContent);\r\n    let followBtn = document.getElementById(\"follow_btn\");\r\n    \r\n\tfollowForm.addEventListener(\"submit\", function (event) {\r\n\t\tevent.preventDefault();\r\n\r\n        const formData = new FormData(followForm);\r\n        \r\n\t\t// isLikeはViews/profile.phpで定義\r\n        if (isFollow) {\r\n            unFollow(formData);\r\n        } else {\r\n            follow(formData);\r\n        }\r\n\t});\r\n\r\n\tfunction follow(formData) {\r\n\t\tfetch('/form/follow', {\r\n\t\t\tmethod: \"POST\",\r\n\t\t\tbody: formData,\r\n\t\t})\r\n\t\t\t.then((response) => response.json())\r\n\t\t\t.then((data) => {\r\n\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\tisFollow = true;\r\n\t\t\t\t\tnumberOfFollower += 1;\r\n\t\t\t\t\tfollowerCount.innerHTML = numberOfFollower;\r\n\t\t\t\t\tfollowBtn.innerHTML = \"フォロー中\";\r\n\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\t// ユーザーにエラーメッセージを表示します\r\n\t\t\t\t\tconsole.error(data.message);\r\n\t\t\t\t\talert(\"Update failed: \" + data.message);\r\n\t\t\t\t}\r\n\t\t\t})\r\n\t\t\t.catch((error) => {\r\n\t\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t\t});\r\n\t}\r\n\r\n\tfunction unFollow(formData) {\r\n\t\tfetch('form/unFollow', {\r\n\t\t\tmethod: \"POST\",\r\n\t\t\tbody: formData,\r\n\t\t})\r\n\t\t\t.then((response) => response.json())\r\n\t\t\t.then((data) => {\r\n\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\tisFollow = false;\r\n\t\t\t\t\tnumberOfFollower -= 1;\r\n\t\t\t\t\tfollowerCount.innerHTML = numberOfFollower;\r\n\t\t\t\t\tfollowBtn.innerHTML = \"フォローする\";\r\n\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\t// ユーザーにエラーメッセージを表示します\r\n\t\t\t\t\tconsole.error(data.message);\r\n\t\t\t\t\talert(\"Update failed: \" + data.message);\r\n\t\t\t\t}\r\n\t\t\t})\r\n\t\t\t.catch((error) => {\r\n\t\t\t\talert(\"An error occurred. Please try again.\");\r\n\t\t\t});\r\n\t}\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/profile.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/assets/js/profile.js"]();
/******/ 	
/******/ })()
;