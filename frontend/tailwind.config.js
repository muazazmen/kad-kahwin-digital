/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: ['selector', '[class*="app-dark"]'],
    content: ['./index.html', './src/**/*.{vue,js}'],
    plugins: [require('tailwindcss-primeui')],
    theme: {
        screens: {
            sm: '576px',
            md: '768px',
            lg: '992px',
            xl: '1200px',
            '2xl': '1920px'
        },
        extend: {
            rotate: {
                'y-180': 'rotateY(180deg)',
                'y-90': 'rotateY(90deg)',
                'y-270': 'rotateY(270deg)',
                'x-180': 'rotateX(180deg)',
                'x-90': 'rotateX(90deg)',
                'x-270': 'rotateX(270deg)'
            },
        },
    },
    experimental: {
        matchArbitraryValues: true
    },
};
