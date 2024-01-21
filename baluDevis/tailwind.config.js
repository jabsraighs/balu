/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ["./assets/**/*.js", "./templates/**/*.html.twig"],
	theme: {
		extend: {
			backgroundColor: {
				secondary: "#2A314B"
			},
			colors: {
				primary: "#487BD1",
				secondary: "#2A314B"
				
			}
		}
	},
	plugins: [require("@tailwindcss/forms")]
}
