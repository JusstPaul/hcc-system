import dayjs from 'dayjs'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import duration from 'dayjs/plugin/duration'
import { StrictMode } from 'react'
import { HelmetProvider } from 'react-helmet-async'
import { createRoot } from 'react-dom/client'
import { createInertiaApp } from '@inertiajs/inertia-react'
import { InertiaProgress } from '@inertiajs/progress'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

import Head from './components/head'

import '../scss/app.scss'

// Configure general libraries
dayjs.extend(utc)
dayjs.extend(timezone)
dayjs.extend(duration)
dayjs.tz.setDefault('Asia/Manila')

// Loading progress indicator
InertiaProgress.init()

// Initialize the system
createInertiaApp({
  resolve: (n) =>
    resolvePageComponent(
      `./pages/${n}.jsx`,
      import.meta.glob('./pages/**/*.jsx'),
    ),
  setup({ el, App, props }) {
    createRoot(el).render(
      <StrictMode>
        <HelmetProvider>
          <Head />
          <App {...props} />
        </HelmetProvider>
      </StrictMode>,
    )
  },
})
