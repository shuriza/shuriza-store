import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                poppins: ['Poppins', 'Inter', 'sans-serif'],
            },
            colors: {
                peri: {
                    lightest: '#e8e6ff',
                    light: '#898ac8',
                    DEFAULT: '#6c63ff',
                    dark: '#5a52d5',
                    darker: '#3d3799',
                    darkest: '#1a1926',
                },
                bluegray: '#1e1d2e',
            },
        },
    },

    plugins: [forms],
};
