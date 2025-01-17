/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.svelte",
        "./node_modules/flowbite-svelte/**/*.{html,js,svelte,ts}",
    ],
    theme: {
        extend: {
            colors: {
                // flowbite-svelte
                primary: {
                    // ori
                    // 50: "#FFF5F2",
                    // 100: "#FFF1EE",
                    // 200: "#FFE4DE",
                    // 300: "#FFD5CC",
                    // 400: "#FFBCAD",
                    // 500: "#FE795D",
                    // 600: "#EF562F",
                    // 700: "#EB4F27",
                    // 800: "#CC4522",
                    // 900: "#A5371B",
                    // custom
                    "50": "#eff6ff",
                    "100": "#dbeafe",
                    "200": "#bfdbfe",
                    "300": "#93c5fd",
                    "400": "#60a5fa",
                    "500": "#3b82f6",
                    "600": "#2563eb",
                    "700": "#1d4ed8",
                    "800": "#1e40af",
                    "900": "#1e3a8a"
                },
            },
        },
    },
    plugins: [require("flowbite/plugin")],
    darkMode: "class",
};
