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

/***/ "./src/assets/js/notifications.js":
/*!****************************************!*\
  !*** ./src/assets/js/notifications.js ***!
  \****************************************/
/***/ (() => {

eval("document.addEventListener(\"DOMContentLoaded\", function () {\r\n\tlet notifications = document.querySelectorAll(\".notification\");\r\n\r\n\tnotifications.forEach(function (notification) {\r\n\t\tnotification.addEventListener(\"click\", function (e) {\r\n\t\t\te.preventDefault();\r\n\r\n\t\t\tlet notificationId = notification.getAttribute(\"data-notification-id\");\r\n\t\t\tlet isRead = notification.getAttribute(\"data-notification-isRead\");\r\n\t\t\tlet formData = new FormData();\r\n\t\t\tformData.append(\"notification_id\", notificationId);\r\n\t\t\tformData.append(\"csrf_token\", csrfToken);\r\n\r\n\t\t\tif (isRead === \"1\") {\r\n\t\t\t\twindow.location.href = notification.href;\r\n\t\t\t} else {\r\n\t\t\t\tfetch(\"/update-isRead\", {\r\n\t\t\t\t\tmethod: \"POST\",\r\n\t\t\t\t\tbody: formData,\r\n\t\t\t\t})\r\n\t\t\t\t\t.then((response) => response.json())\r\n\t\t\t\t\t.then((data) => {\r\n\t\t\t\t\t\tif (data.status === \"success\") {\r\n\t\t\t\t\t\t\twindow.location.href = notification.href;\r\n\t\t\t\t\t\t} else if (data.status === \"error\") {\r\n\t\t\t\t\t\t\talert(data.message);\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t})\r\n\t\t\t\t\t.catch((error) => {\r\n\t\t\t\t\t\talert(\"エラーが発生しました\");\r\n\t\t\t\t\t});\r\n\t\t\t}\r\n\t\t});\r\n\t});\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/notifications.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/assets/js/notifications.js"]();
/******/ 	
/******/ })()
;