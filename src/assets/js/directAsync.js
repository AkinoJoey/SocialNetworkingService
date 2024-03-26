document.addEventListener("DOMContentLoaded", function () {
	window.scrollTo(0, document.body.scrollHeight);

	let conn = new WebSocket("ws://localhost:8080");
	conn.onopen = function (e) {
		let data = {
			type: "join",
			dm_thread_id: dmThreadId,
			sender_user_id: senderUserId,
			receiver_user_id: receiverUserId,
		};

		conn.send(JSON.stringify(data));
	};

	const chatContainer = document.getElementById("chat_container");
	conn.onmessage = function (e) {
		let data = JSON.parse(e.data);
		if (data.status === "success") {
			chatContainer.innerHTML += `
            <div class="chat chat-start">
                    <div class="avatar chat-image">
                        <div class="w-10 rounded-full">
                            <img alt="Tailwind CSS chat bubble component" src="${receiverUserProfileImagePath}" />
                        </div>
                    </div>
                    <div class="chat-header">
                        ${receiverUserAccountName}
                        <time class="text-xs opacity-50">12:45</time>
                    </div>
                    <div class="chat-bubble text-black bg-gray-300 dark:bg-gray-700">${data.message}</div>
                    <div class="chat-footer opacity-50">Delivered</div>
            </div>
            `;
			window.scrollTo(0, document.body.scrollHeight);
		}
		if (data.status === "error") {
			alert(data.message);
		}
	};

	conn.onerror = function () {
        alert("エラーが発生しました");
        window.location.reload();
	};

	document.getElementById("submit_btn").addEventListener("click", function (e) {
		e.preventDefault();
		send();
	});

	let chatTextArea = document.getElementById("message");
	chatTextArea.focus();

	chatTextArea.addEventListener("keydown", function (e) {
		if (e.key === "Enter" && e.shiftKey) {
			e.preventDefault();
			send();
		}
	});

    function send() {
		if (chatTextArea.value === "") return;

		let maxLength = 140;

		if (chatTextArea.value.length > maxLength || chatTextArea.value.trim() === "") {
            alert("1文字以上140文字以内のテキストのみ送信可能です");
		} else {
			let data = {
				type: "message",
				dm_thread_id: dmThreadId,
				sender_user_id: senderUserId,
				receiver_user_id: receiverUserId,
				message: chatTextArea.value,
			};

			conn.send(JSON.stringify(data));

			chatContainer.innerHTML += `
            <div class="chat chat-end">
                <div class="avatar chat-image">
                    <div class="w-10 rounded-full">
                        <img alt="Tailwind CSS chat bubble component" src="${senderUserProfileImagePath}" />
                    </div>
                </div>
                <div class="chat-header">
                    ${senderUserAccountName}
                </div>
                <div class="chat-bubble text-white bg-blue-400">${chatTextArea.value}</div>
                <div class="chat-footer opacity-50">${new Date().toLocaleDateString("ja-JP", { year: "numeric", month: "2-digit", day: "2-digit", hour: "2-digit", minute: "2-digit" })}</div>
            </div>
            `;
			chatTextArea.value = "";
			window.scrollTo(0, document.body.scrollHeight);
		}
	}
});
