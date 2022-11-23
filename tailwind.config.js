/** @type {import('tailwindcss/defaultTheme')} */
const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss/colors')} */
const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.jsx',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["'Inter'", ...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: colors.green,
      },
    },
  },
  plugins: [require('@tailwindcss/forms')],
}
