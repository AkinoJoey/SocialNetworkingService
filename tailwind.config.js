/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ["./src/views/*/*.{html,js,php}", "./node_modules/flowbite/**/*.js"],
	theme: {
		extend: {
			animation: {
				"wobble-hor-bottom": "wobble-hor-bottom 0.8s ease   both",
				"fade-in": "fade-in 1.2s cubic-bezier(0.390, 0.575, 0.565, 1.000)   both",
			},
			keyframes: {
				"wobble-hor-bottom": {
                    "0%,to": {
                        transform: "translateX(0%)",
                        "transform-origin": "50% 50%"
                    },
                    "15%": {
                        transform: "translateX(-30px) rotate(-6deg)"
                    },
                    "30%": {
                        transform: "translateX(15px) rotate(6deg)"
                    },
                    "45%": {
                        transform: "translateX(-15px) rotate(-3.6deg)"
                    },
                    "60%": {
                        transform: "translateX(9px) rotate(2.4deg)"
                    },
                    "75%": {
                        transform: "translateX(-6px) rotate(-1.2deg)"
                    }
                },
				"fade-in": {
					"0%": {
						opacity: "0",
					},
					to: {
						opacity: "1",
					},
				},
			},
		},
	},
	plugins: [require("flowbite/plugin"), require("daisyui")],
};
