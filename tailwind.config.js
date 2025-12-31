/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#4A7C23',
                    600: '#2D5016',
                    700: '#243f12',
                    800: '#1a2e0d',
                    900: '#111e09',
                },
                secondary: {
                    500: '#1E3A5F',
                    600: '#152a45',
                },
                accent: {
                    100: '#ffedd5',
                    500: '#E87B35',
                    600: '#c4612a',
                },
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
