import { createApp } from 'vue'
import CreateForm from '../components/CreateForm.vue'
import { registerTranslations } from '../translations'

export function mountCreateForm(selector, props = {}) {
  const container = document.querySelector(selector)
  if (!container) {
    console.error(`Container not found: ${selector}`)
    return
  }

  const app = createApp(CreateForm, {
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
    isAuthenticated: props.isAuthenticated || false,
    hashPattern: props.hashPattern || '[a-zA-Z0-9_-]{1,16}',
    loginRoute: props.loginRoute,
    dashboardRoute: props.dashboardRoute
  })

  registerTranslations(app)
  app.mount(selector)
  return app
}

const container = document.getElementById('createFormApp')

if (container) {
  mountCreateForm('#createFormApp', {
    isAuthenticated: container.dataset.authenticated === 'true',
    hashPattern: container.dataset.hashPattern,
    loginRoute: container.dataset.loginRoute,
    dashboardRoute: container.dataset.dashboardRoute
  })
}
