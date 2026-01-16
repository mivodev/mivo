/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.{html,js,php}",
    "./public/assets/js/**/*.js",
    "./public/js/**/*.js"
  ],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        sans: ['Geist', 'sans-serif'],
        mono: ['Geist Mono', 'monospace'],
      },
      colors: {
        background: 'var(--background)',
        foreground: 'var(--foreground)',
        accents: {
          1: 'var(--accents-1)',
          2: 'var(--accents-2)',
          3: 'var(--accents-3)',
          4: 'var(--accents-4)',
          5: 'var(--accents-5)',
          6: 'var(--accents-6)',
          7: 'var(--accents-7)',
          8: 'var(--accents-8)',
        },
        success: '#0070f3',
        danger: '#e00',
        warning: '#f5a623',
      }
    },
  },
  plugins: [],
}
