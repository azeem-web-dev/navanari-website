import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
                serif: ['"Playfair Display"', ...defaultTheme.fontFamily.serif],
                script: ['"Dancing Script"', 'cursive'],
            },
            colors: {
                // Feminine boutique palette.
                rose: {
                    50: '#fff1f5',
                    100: '#ffe4ec',
                    200: '#fecdd9',
                    300: '#fda4bd',
                    400: '#fb7199',
                    500: '#f43f74',
                    600: '#e11d62',
                    700: '#be185d',
                    800: '#9d174d',
                    900: '#831843',
                },
                gold: {
                    DEFAULT: '#d4af37',
                    light: '#e8cd76',
                    dark: '#b8962b',
                },
                cream: '#fdf8f4',
                ink: '#2a1a22',
            },
            boxShadow: {
                soft: '0 10px 40px -12px rgba(190, 24, 93, 0.22)',
                glow: '0 0 0 1px rgba(212, 175, 55, 0.25), 0 18px 50px -18px rgba(190, 24, 93, 0.4)',
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(28px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'scale-in': {
                    '0%': { opacity: '0', transform: 'scale(.92)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                float: {
                    '0%,100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-12px)' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-700px 0' },
                    '100%': { backgroundPosition: '700px 0' },
                },
                marquee: {
                    '0%': { transform: 'translateX(0)' },
                    '100%': { transform: 'translateX(-50%)' },
                },
                'spin-slow': {
                    '0%': { transform: 'rotate(0deg)' },
                    '100%': { transform: 'rotate(360deg)' },
                },
            },
            animation: {
                'fade-up': 'fade-up .7s cubic-bezier(.22,1,.36,1) both',
                'fade-in': 'fade-in 1s ease both',
                'scale-in': 'scale-in .6s cubic-bezier(.22,1,.36,1) both',
                float: 'float 6s ease-in-out infinite',
                shimmer: 'shimmer 2s linear infinite',
                marquee: 'marquee 24s linear infinite',
                'spin-slow': 'spin-slow 18s linear infinite',
            },
        },
    },

    plugins: [forms],
};
