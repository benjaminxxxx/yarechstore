import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: '#263238', // Color principal
                primaryDark: '#1d4ed8', // Color principal para el modo oscuro
                primaryText: '#ffffff', // Color de texto para el modo claro
                primaryTextGray: 'rgb(194 194 194)',
                primaryDarkText: '#f3f4f6', // Color de texto para el modo oscuro
                primaryHoverOpacity: '#313D43', // Color de fondo con opacidad para hover en modo claro
                primaryDarkHoverOpacity: 'rgba(29, 78, 216, 0.8)', // Color de fondo con opacidad para hover en modo oscuro
                primaryFocus: '#22428D', // Color del anillo de enfoque en modo claro
                primaryDarkFocus: '#1d4ed8', // Color del anillo de enfoque en modo oscuro
                bodydark: '#7A9985',
                bodydark1: '#E0E0E0',
                bodydark2: '#E0E0E0',
                grayNormal: '#C6C6C6',
        
                secondary: '#F5922A', // Color secundario
                secondaryDark: '#F5681F', // Color secundario para el modo oscuro
                secondaryText: '#ffffff', // Color de texto para el modo claro en estado secundario
                secondaryDarkText: '#f3f4f6', // Color de texto para el modo oscuro en estado secundario
                secondaryHoverOpacity: 'rgba(245, 146, 42, 0.8)', // Color de fondo con opacidad para hover en estado secundario (modo claro)
                secondaryDarkHoverOpacity: 'rgba(245, 104, 31, 0.8)', // Color de fondo con opacidad para hover en estado secundario (modo oscuro)
                secondaryFocus: '#F5922A', // Color del anillo de enfoque en estado secundario
                secondaryDarkFocus: '#F5681F', // Color del anillo de enfoque en estado secundario (modo oscuro)
              },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};
