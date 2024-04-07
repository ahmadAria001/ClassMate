import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.postcss", "resources/js/app.js"],
            refresh: true,
        }),
        svelte({}),
    ],
    resolve: {
        alias: {
            "@P": path.resolve(__dirname, "resources/js/Pages"),
            "@R": path.resolve(__dirname, "resources/js/"),
            "@C": path.resolve(__dirname, "resources/js/Components"),
        },
    },
    //  (name) => {
    //     const pages = import.meta.glob("./Pages/**/*.svelte", { eager: true });
    //     return pages[`./Pages/${name}.svelte`];
    // },
});
