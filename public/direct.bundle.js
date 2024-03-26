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

/***/ "./src/assets/js/directAsync.js":
/*!**************************************!*\
  !*** ./src/assets/js/directAsync.js ***!
  \**************************************/
/***/ (() => {

eval("document.addEventListener(\"DOMContentLoaded\", function () {\r\n\twindow.scrollTo(0, document.body.scrollHeight);\r\n\r\n\tlet conn = new WebSocket(\"ws://localhost:8080\");\r\n\tconn.onopen = function (e) {\r\n\t\tlet data = {\r\n\t\t\ttype: \"join\",\r\n\t\t\tdm_thread_id: dmThreadId,\r\n\t\t\tsender_user_id: senderUserId,\r\n\t\t\treceiver_user_id: receiverUserId,\r\n\t\t};\r\n\r\n\t\tconn.send(JSON.stringify(data));\r\n\t};\r\n\r\n\tconst chatContainer = document.getElementById(\"chat_container\");\r\n\tconn.onmessage = function (e) {\r\n\t\tlet data = JSON.parse(e.data);\r\n\t\tif (data.status === \"success\") {\r\n\t\t\tchatContainer.innerHTML += `\r\n            <div class=\"chat chat-start\">\r\n                    <div class=\"avatar chat-image\">\r\n                        <div class=\"w-10 rounded-full\">\r\n                            <img alt=\"Tailwind CSS chat bubble component\" src=\"${receiverUserProfileImagePath}\" />\r\n                        </div>\r\n                    </div>\r\n                    <div class=\"chat-header\">\r\n                        ${receiverUserAccountName}\r\n                        <time class=\"text-xs opacity-50\">12:45</time>\r\n                    </div>\r\n                    <div class=\"chat-bubble text-black bg-gray-300 dark:bg-gray-700\">${data.message}</div>\r\n                    <div class=\"chat-footer opacity-50\">Delivered</div>\r\n            </div>\r\n            `;\r\n\t\t\twindow.scrollTo(0, document.body.scrollHeight);\r\n\t\t}\r\n\t\tif (data.status === \"error\") {\r\n\t\t\talert(data.message);\r\n\t\t}\r\n\t};\r\n\r\n\tconn.onerror = function () {\r\n        alert(\"エラーが発生しました\");\r\n        window.location.reload();\r\n\t};\r\n\r\n\tdocument.getElementById(\"submit_btn\").addEventListener(\"click\", function (e) {\r\n\t\te.preventDefault();\r\n\t\tsend();\r\n\t});\r\n\r\n\tlet chatTextArea = document.getElementById(\"message\");\r\n\tchatTextArea.focus();\r\n\r\n\tchatTextArea.addEventListener(\"keydown\", function (e) {\r\n\t\tif (e.key === \"Enter\" && e.shiftKey) {\r\n\t\t\te.preventDefault();\r\n\t\t\tsend();\r\n\t\t}\r\n\t});\r\n\r\n    function send() {\r\n\t\tif (chatTextArea.value === \"\") return;\r\n\r\n\t\tlet maxLength = 140;\r\n\r\n\t\tif (chatTextArea.value.length > maxLength || chatTextArea.value.trim() === \"\") {\r\n            alert(\"1文字以上140文字以内のテキストのみ送信可能です\");\r\n\t\t} else {\r\n\t\t\tlet data = {\r\n\t\t\t\ttype: \"message\",\r\n\t\t\t\tdm_thread_id: dmThreadId,\r\n\t\t\t\tsender_user_id: senderUserId,\r\n\t\t\t\treceiver_user_id: receiverUserId,\r\n\t\t\t\tmessage: chatTextArea.value,\r\n\t\t\t};\r\n\r\n\t\t\tconn.send(JSON.stringify(data));\r\n\r\n\t\t\tchatContainer.innerHTML += `\r\n            <div class=\"chat chat-end\">\r\n                <div class=\"avatar chat-image\">\r\n                    <div class=\"w-10 rounded-full\">\r\n                        <img alt=\"Tailwind CSS chat bubble component\" src=\"${senderUserProfileImagePath}\" />\r\n                    </div>\r\n                </div>\r\n                <div class=\"chat-header\">\r\n                    ${senderUserAccountName}\r\n                </div>\r\n                <div class=\"chat-bubble text-white bg-blue-400\">${chatTextArea.value}</div>\r\n                <div class=\"chat-footer opacity-50\">${new Date().toLocaleDateString(\"ja-JP\", { year: \"numeric\", month: \"2-digit\", day: \"2-digit\", hour: \"2-digit\", minute: \"2-digit\" })}</div>\r\n            </div>\r\n            `;\r\n\t\t\tchatTextArea.value = \"\";\r\n\t\t\twindow.scrollTo(0, document.body.scrollHeight);\r\n\t\t}\r\n\t}\r\n});\r\n\n\n//# sourceURL=webpack:///./src/assets/js/directAsync.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/assets/js/directAsync.js"]();
/******/ 	
/******/ })()
;