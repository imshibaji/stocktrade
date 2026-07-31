/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./app/Views/**/*.php'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Lora', 'Georgia', 'ui-serif', 'Georgia', 'serif'],
        heading: ['Poppins', 'Arial', 'ui-sans-serif', 'sans-serif'],
      },
      colors: {
        page: 'rgb(var(--bg-page) / <alpha-value>)',
        surface: 'rgb(var(--bg-surface) / <alpha-value>)',
        accent: 'rgb(var(--accent) / <alpha-value>)',
        'accent-2': 'rgb(var(--accent-2) / <alpha-value>)',
        'on-accent': 'rgb(var(--on-accent) / <alpha-value>)',
        ink: {
          100: 'rgb(var(--ink) / <alpha-value>)',
          200: 'rgb(var(--ink-2) / <alpha-value>)',
          300: 'rgb(var(--ink-3) / <alpha-value>)',
          400: 'rgb(var(--ink-4) / <alpha-value>)',
          500: 'rgb(var(--ink-5) / <alpha-value>)',
          600: 'rgb(var(--ink-6) / <alpha-value>)',
        },
        line: {
          DEFAULT: 'rgb(var(--border) / <alpha-value>)',
          strong: 'rgb(var(--border-strong) / <alpha-value>)',
          soft: 'rgb(var(--border) / 0.5)',
        },
        pos: 'rgb(var(--pos) / <alpha-value>)',
        'pos-soft': 'rgb(var(--pos-soft) / <alpha-value>)',
        neg: 'rgb(var(--neg) / <alpha-value>)',
        'neg-soft': 'rgb(var(--neg-soft) / <alpha-value>)',
        link: 'rgb(var(--blue-soft) / <alpha-value>)',
        white: 'rgb(var(--ink) / <alpha-value>)',
      },
    },
  },
  plugins: [],
};
