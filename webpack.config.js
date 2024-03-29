const path = require("path");
const webpack = require("webpack");

const isDev = process.env.NODE_ENV === "development";

module.exports = {
	mode: isDev ? "development" : "production",
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
		guest: "./src/assets/js/guest.js",
		verifyForgotPassword: "./src/assets/js/verifyForgotPassword.js",
		signup: "./src/assets/js/signup.js",
		login: "./src/assets/js/login.js",
		forgotPassword: "./src/assets/js/forgotPassword.js",
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
	plugins: [
		new webpack.DefinePlugin({
			DEVELOPMENT: JSON.stringify(isDev),
		}),
	],
};
