import { createApp, h } from 'vue'
import { NNotificationProvider, NConfigProvider } from 'naive-ui'
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
import { ZiggyVue } from 'ziggy'
import dayjs from 'dayjs'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import duration from 'dayjs/plugin/duration'

import 'vfonts/Lato.css'
import 'vfonts/FiraCode.css'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

import '../css/app.css'

// Configure general libraries
dayjs.extend(utc)
dayjs.extend(timezone)
dayjs.extend(duration)
dayjs.tz.setDefault('Asia/Manila')

// Loading progress indicator
InertiaProgress.init()

// Theme
const themeOverrides = {
  common: {
    primaryColor: '#14b8a6',
  },
}

// Initialize the system
createInertiaApp({
  resolve: (n) =>
    resolvePageComponent(
      `./Pages/${n}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ),
  setup({ el, app, props, plugin }) {
    const pinia = createPinia()

    createApp({
      // Notifications
      render: () =>
        h(
          NNotificationProvider,
          {},
          {
            // Main App
            default: () =>
              h(
                NConfigProvider,
                {
                  themeOverrides,
                  style: {
                    height: '100%',
                  },
                },
                {
                  default: () => h(app, props),
                },
              ),
          },
        ),
    })
      .use(pinia)
      .use(plugin)
      .use(ZiggyVue)
      .component('i-head', Head)
      .component('i-link', Link)
      .mount(el)
  },
})
