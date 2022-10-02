import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import vuePugPlugin from "vue-pug-plugin";
import path from "path";
import { defineConfig } from "vite";

export default defineConfig({
  build: {
    target: 'es2015',
  },
  plugins: [
    vue({
      template: {
        preprocessOptions: {
          plugins: [
            vuePugPlugin
          ]
        }
      }
    }),
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
