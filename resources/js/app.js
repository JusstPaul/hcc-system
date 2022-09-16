import { createApp, h } from "vue";
import { createInertiaApp, Head, Link } from "@inertiajs/inertia-vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "ziggy";

import "@vant/touch-emulator";
import "vant/lib/index.css";

// Some helpers

// Initialize the system
createInertiaApp({
    resolve: (n) =>
        resolvePageComponent(
            `./Pages/${n}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, app, props, plugin }) {
        createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component("i-head", Head)
            .component("i-link", Link)
            .mount(el);
    },
});
