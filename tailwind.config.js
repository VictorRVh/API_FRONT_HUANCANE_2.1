/** @type {import('tailwindcss').Config} */
import animationDelay from 'tailwindcss-animation-delay';

export default {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      screens: {
        xs: '470px',
        mobile: '420px',
      },
      fontFamily: {
        inter: ['Inter', 'sans-serif'],
      },
      fontSize: {
        xsm: '13px',
        '2xs': '11px',
        xxs: '10px',
      },
      colors: {
        // Paleta de colores para modo claro
        granate: '#921733',
        plomoClaro: '#F8F8FA',
        blancoPuro: '#FFFFFF',
        azulado: '#F8F8FA',
        negroClaro: '#49454F',
        active: '#921733',
        // Colores para modo oscuro
        dark: {
          fondo: '#121212',       // Fondo oscuro general
          surface: '#1E1E1E',     // Fondo para tarjetas y componentes
          text: '#E0E0E0',        // Color de texto
          primary: '#BB86FC',     // Color primario accesible en fondo oscuro
          secondary: '#03DAC6',   // Color secundario en modo oscuro
        },
      },
      transitionProperty: {
        width: 'width',
        height: 'height',
        'max-height': 'max-height',
      },

      borderRadius: {
        sm: '4px',
      },
      animationDelay: {
        100: '100ms',
        200: '200ms',
        300: '300ms',
        400: '400ms',
        500: '500ms',
        600: '600ms',
        700: '700ms',
        800: '800ms',
        900: '900ms',
        1000: '1000ms',
        1100: '1100ms',
        1200: '1200ms',
        1300: '1300ms',
        1400: '1400ms',
        1500: '1500ms',
        1600: '1600ms',
      },
    },
  },
  plugins: [animationDelay],
};
