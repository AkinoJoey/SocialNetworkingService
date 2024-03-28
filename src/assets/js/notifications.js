document.addEventListener("DOMContentLoaded", function () {
	let notifications = document.querySelectorAll(".notification");

	notifications.forEach(function (notification) {
		notification.addEventListener("click", function (e) {
			e.preventDefault();

			let notificationId = notification.getAttribute("data-notification-id");
			let isRead = notification.getAttribute("data-notification-isRead");
			let formData = new FormData();
			formData.append("notification_id", notificationId);
			formData.append("csrf_token", csrfToken);

			if (isRead === "1") {
				window.location.href = notification.href;
			} else {
				fetch("/update-isRead", {
					method: "POST",
					body: formData,
				})
					.then((response) => response.json())
					.then((data) => {
						if (data.status === "success") {
							window.location.href = notification.href;
						} else if (data.status === "error") {
							alert(data.message);
						}
					})
					.catch((error) => {
						alert("エラーが発生しました");
					});
			}
		});
	});
});
