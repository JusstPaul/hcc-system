import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import Components from "unplugin-vue-components/vite";
import path from "path";
import { defineConfig } from "vite";
import { VantResolver } from "unplugin-vue-components/resolvers";

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//     ],
// });
//
export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/js/app.js"],
        }),
        Components({
            resolvers: [VantResolver()],
        }),
    ],
});
