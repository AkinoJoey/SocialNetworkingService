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

eval("document.addEventListener('DOMContentLoaded', function () {\r\n    window.scrollTo(0, document.body.scrollHeight);\r\n    \r\n    let conn = new WebSocket('ws://localhost:8080');\r\n    conn.onopen = function(e) {\r\n        let data = {\r\n            type: 'join',\r\n            dm_thread_id: dmThreadId,\r\n            sender_user_id: senderUserId,\r\n            receiver_user_id: receiverUserId,\r\n        };\r\n\r\n        conn.send(JSON.stringify(data));\r\n    };\r\n\r\n    const chatContainer = document.getElementById('chat_container');\r\n    conn.onmessage = function(e) {\r\n        chatContainer.innerHTML +=\r\n            `\r\n        <div class=\"chat chat-start\">\r\n                <div class=\"avatar chat-image\">\r\n                    <div class=\"w-10 rounded-full\">\r\n                        <img alt=\"Tailwind CSS chat bubble component\" src=\"https://daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg\" />\r\n                    </div>\r\n                </div>\r\n                <div class=\"chat-header\">\r\n                    ${receiverUserAccountName}\r\n                    <time class=\"text-xs opacity-50\">12:45</time>\r\n                </div>\r\n                <div class=\"chat-bubble text-black bg-gray-300 dark:bg-gray-700\">${e.data}</div>\r\n                <div class=\"chat-footer opacity-50\">Delivered</div>\r\n        </div>\r\n        `\r\n        window.scrollTo(0, document.body.scrollHeight);\r\n\r\n    };\r\n\r\n\r\n    document.getElementById('submit_btn').addEventListener('submit', function(e) {\r\n        send()\r\n        e.preventDefault();\r\n    });\r\n\r\n    let chatTextArea = document.getElementById('message');\r\n\r\n    chatTextArea.addEventListener(\"keydown\", function(e) {\r\n        if (e.key === 'Enter' && e.shiftKey) {\r\n            send()\r\n            e.preventDefault();\r\n        }\r\n    })\r\n\r\n    function send() {\r\n        if (chatTextArea.value === '') return;\r\n\r\n        let data = {\r\n            type: 'message',\r\n            dm_thread_id: dmThreadId,\r\n            sender_user_id: senderUserId,\r\n            receiver_user_id: receiverUserId,\r\n            message: chatTextArea.value\r\n        };\r\n\r\n        conn.send(JSON.stringify(data));\r\n\r\n        chatContainer.innerHTML +=\r\n            `\r\n            <div class=\"chat chat-end\">\r\n                <div class=\"avatar chat-image\">\r\n                    <div class=\"w-10 rounded-full\">\r\n                        <img alt=\"Tailwind CSS chat bubble component\" src=\"https://daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg\" />\r\n                    </div>\r\n                </div>\r\n                <div class=\"chat-header\">\r\n                    ${senderUserAccountName}\r\n                    <time class=\"text-xs opacity-50\">12:46</time>\r\n                </div>\r\n                <div class=\"chat-bubble text-white bg-blue-400\">${chatTextArea.value}</div>\r\n                <div class=\"chat-footer opacity-50\">Seen at 12:46</div>\r\n            </div>\r\n            `\r\n        chatTextArea.value = \"\";\r\n        window.scrollTo(0, document.body.scrollHeight);\r\n    }\r\n})\n\n//# sourceURL=webpack:///./src/assets/js/directAsync.js?");

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