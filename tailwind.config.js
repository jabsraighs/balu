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
				primary: {
					100: "#B2C7F6",
					200: "#8DAFF0",
					300: "#6797EA",
					400: "#3F7FDD",
					500: "#2E6BC4", // Couleur principale
					600: "#26589A",
					700: "#1C436F",
					800: "#132D45",
					900: "#0A1920",
				},
				secondary: {
					100: "#A4AABF",
					200: "#868C9E",
					300: "#696E7E",
					400: "#4B5061",
					500: "#2A314B", // Couleur secondaire
					600: "#202535",
					700: "#181D2A",
					800: "#10141E",
					900: "#080A10",
				},
				delete: {
					100: "rgba(205,32,32,0.16)",
					200: "rgba(205,32,32,0.32)",
					300: "rgba(205,32,32,0.48)",
					400: "rgba(205,32,32,0.64)",
					500: "rgba(205,32,32,0.8)", // Couleur delete
					600: "rgba(205,32,32,0.88)",
					700: "rgba(205,32,32,0.96)",
					800: "#CD2020",
					900: "#9C1616",
				},
				background: {
					100: "#FFFFFF",
					200: "#F3F6FC",
					300: "#E6ECF9",
					400: "#D9E1F6",
					500: "#F8FAFF", // Couleur de fond
					600: "#BAC9F3",
					700: "#8CA3E9",
					800: "#5D7CDE",
					900: "#2F55D4",
				},
				gray: {
					100: "#F2F2F2",
					200: "#E6E6E6",
					300: "#D9D9D9",
					400: "#BFBFBF",
					500: "#AAAAAA", // Couleur gray
					600: "#8A8A8A",
					700: "#6B6B6B",
					800: "#4D4D4D",
					900: "#2E2E2E",
				},
			},
			width: {
				card: "20.5rem",
				sidebar: "300px",
				"10p": "10%",
			},
			minWidth: {
				card: "20.5rem"
			},
			height: {
				card: "9.375rem",
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
			maxWidth: {
				"2xs": "19rem",
			}
		}
	},
	plugins: [require("@tailwindcss/forms")]
}
