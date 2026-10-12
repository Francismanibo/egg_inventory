import forms from '@tailwindcss/forms';

export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                ink: '#163765',
                primary: '#2878d4',
                'primary-dark': '#1c5fab',
                mist: '#f3f8ff',
                line: '#d8e5f3',
            },
        },
    },

    plugins: [
        forms,
    ],
};