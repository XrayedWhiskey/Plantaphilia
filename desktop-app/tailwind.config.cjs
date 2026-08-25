/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./src/renderer/**/*.{js,jsx,ts,tsx,html}'],
  theme: {
    extend: {
      colors: {
        'creme': '#EAE4D6',
        'creme-dim': '#C8C0AF',
        'creme-muted': '#9CA59E',
        'plum': '#6B3FA0',
        'plum-hot': '#9B6FD0',
        'lavender': '#B8A8D8',
        'bg-surface': '#2F3B35',
        'bg-deep': '#1A231F',
        'bg-inky': '#0F1612',
        'bg-raised': '#3A4840',
        'border-thin': 'rgba(155,111,208,0.2)',
        'border-hair': 'rgba(155,111,208,0.1)',
        'burgundy': '#5A1A2E',
        'burgundy-hot': '#7A2840',
      },
      fontFamily: {
        sans: ['Montserrat', 'system-ui', 'sans-serif'],
        serif: ['Playfair Display', 'Georgia', 'serif'],
      },
    },
  },
  plugins: [],
}
