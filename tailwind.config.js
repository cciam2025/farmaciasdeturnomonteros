// tailwind.config.js

module.exports = {
  content: [
    "./public/**/*.{html,js,php}", // Busca clases en todos estos archivos
  ],
  theme: {
    extend: {
      colors: {
        'moss-green': '#8A9A5B', // Este es el verde musgo
        'gold-custom': '#D4AF37',  // Este es el dorado
      },
      fontFamily: {
        'sans': ['Inter', 'sans-serif'], // Fuente moderna
      }
    },
  },
  plugins: [],
}