const path = require("path");

module.exports = {
	entry: {
		main: "./src/assets/js/index.js",
		posts: "./src/assets/js/posts.js",
		profile: "./src/assets/js/profile.js",
		direct: "./src/assets/js/directAsync.js",
		notifications: "./src/assets/js/notifications.js",
		top: "./src/assets/js/top.js",
		loggedInUser: "./src/assets/js/postAndLogoutModal.js",
		searchUser: "./src/assets/js/searchUser.js",
		profileEdit: "./src/assets/js/profileEdit.js",
		scheduledPosts: "./src/assets/js/scheduledPosts.js",
		guest: "./src/assets/js/guest.js"
	},
	output: {
		path: path.resolve(__dirname, "public"),
		filename: "[name].bundle.js",
	},
	
	module: {
		rules: [
			{
				test: /\.css$/i,
				use: ["style-loader", "css-loader", "postcss-loader"],
			},
		],
	},
};
