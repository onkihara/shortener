import { createApp } from 'vue'
import Dashboard from '../components/Dashboard.vue'

export function mountDashboard(selector, props = {}) {
  const container = document.querySelector(selector)
  if (!container) {
    console.error(`Container not found: ${selector}`)
    return
  }

  const app = createApp(Dashboard, {
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
    timezone: props.timezone || 'UTC',
    createRoute: props.createRoute
  })

  app.mount(selector)
  return app
}

const container = document.getElementById('dashboardApp')

if (container) {
  mountDashboard('#dashboardApp', {
    timezone: container.dataset.timezone,
    createRoute: container.dataset.createRoute
  })
}
