const path = require("path");

module.exports = {
	entry: "./src/assets/js/index.js",
	output: {
		path: path.resolve(__dirname, "public"),
		filename: "bundle.js",
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
