import { createApp } from 'vue'
import Hero from '../components/Hero.vue'
import { registerTranslations } from '../translations'

export function mountHero(selector, props = {}) {
  const container = document.querySelector(selector)
  if (!container) {
    console.error(`Container not found: ${selector}`)
    return
  }

  const app = createApp(Hero, {
    isAuthenticated: props.isAuthenticated || false,
    loginRoute: props.loginRoute || 'login',
    dashboardRoute: props.dashboardRoute || 'dashboard',
    scrollTarget: props.scrollTarget || '#createFormApp'
  })

  registerTranslations(app)
  app.mount(selector)
  return app
}

const container = document.getElementById('heroApp')

if (container) {
  mountHero('#heroApp', {
    isAuthenticated: container.dataset.authenticated === 'true',
    loginRoute: container.dataset.loginRoute,
    dashboardRoute: container.dataset.dashboardRoute,
    scrollTarget: container.dataset.scrollTarget || '#createFormApp'
  })
}
