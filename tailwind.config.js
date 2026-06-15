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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                cream: {
                    50: '#FFFDF5',
                    100: '#FFF8E7',
                    200: '#FFF0CC',
                    300: '#FFE4A3',
                    400: '#FFD56B',
                    500: '#FFC233',
                    600: '#F5A623',
                    700: '#D48B1A',
                    800: '#A86E14',
                    900: '#7A5110',
                },
                amber: {
                    50: '#FFF8E7',
                    100: '#FFEDC3',
                    200: '#FFD893',
                    300: '#FFBE5C',
                    400: '#FFA333',
                    500: '#FF8700',
                    600: '#E67500',
                    700: '#C26200',
                    800: '#9B4F00',
                    900: '#7A3D00',
                },
                orange: {
                    50: '#FFF5ED',
                    100: '#FFE6D2',
                    200: '#FFC9A3',
                    300: '#FFA66B',
                    400: '#FF823A',
                    500: '#FF6310',
                    600: '#E64E00',
                    700: '#C24000',
                    800: '#9E3400',
                    900: '#7D2900',
                },
            },
        },
    },

    plugins: [forms],
};
