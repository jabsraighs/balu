/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
     "./assests/**/*.js",
     "./templates/**/*.html.twig;",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}

