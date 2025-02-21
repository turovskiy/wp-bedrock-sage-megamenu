import containerQueries from '@tailwindcss/container-queries';
import tailwindcssTypography from '@tailwindcss/typography'

/** @type {import('tailwindcss').Config} */
const config = {
  content: [
    './app/**/*.php', 
    './resources/**/*.{php,js}'
  ],
  theme: {
    extend: {
      colors: {}, // Extend Tailwind's default colors
    },
  },
  plugins: [containerQueries, tailwindcssTypography],
};

export default config;