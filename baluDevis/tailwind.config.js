/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ["./assets/**/*.js", "./templates/**/*.html.twig"],
	theme: {
		screens: {
			"sm": "640px",
			"md": "768px",
			"2md": "960px",
			"lg": "1024px",
			"xl": "1280px",
			"2xl": "1536px",
		},
		extend: {
			backgroundColor: {
				secondary: "#2A314B",
                background: "#F8FAFF",
			},
			colors: {
                primary: "#487BD1",
                secondary: "#2A314B",
                delete: "rgba(205,32,32,0.8)",
                background: "#F8FAFF",
                gray: "#AAAAAA"
			},
			width: {
				card: "20.5rem",
				sidebar: "300px",
			},
			height: {
				card: "16.375rem",
			},
			borderColor: {
				primaryLight: "rgba(72,123,209, 0.1)",
			},
			boxShadow: {
                small: "rgba(42,49,75,0.2) 0px 2px 4px 0px"
            },
            borderRadius: {
                "2xl": "4rem"
            },
            padding: {
                side: "300px"
            },
		}
	},
	plugins: [require("@tailwindcss/forms")]
}
