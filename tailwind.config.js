/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ["./src/views/*/*.{html,js,php}", "./node_modules/flowbite/**/*.js"],
	theme: {
		extend: {},
	},
	plugins: [require("flowbite/plugin"), require("daisyui")],
};
