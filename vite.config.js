import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import path from "path";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/js/app.js"],
        }),
    ],
    resolve: {
        alias: {
            ziggy: path.resolve(
                __dirname,
                "vendor/tightenco/ziggy/dist/vue.es.js"
            ),
            "@": path.resolve(__dirname, "resources/js"),
        },
    },
});
