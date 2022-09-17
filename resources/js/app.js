import { createApp, h } from "vue";
import { createInertiaApp, Head, Link } from "@inertiajs/inertia-vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createPinia } from "pinia";
import { ZiggyVue } from "ziggy";

import "vfonts/Lato.css";
import "vfonts/FiraCode.css";

// Some helpers

// Initialize the system
createInertiaApp({
    resolve: (n) =>
        resolvePageComponent(
            `./Pages/${n}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, app, props, plugin }) {
        const pinia = createPinia();

        createApp({ render: () => h(app, props) })
            .use(pinia)
            .use(plugin)
            .use(ZiggyVue)
            .component("i-head", Head)
            .component("i-link", Link)
            .mount(el);
    },
});
