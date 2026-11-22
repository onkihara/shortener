export function t(key, replacements = {}) {
  const translation = window.translations[key]
  if (typeof translation !== 'string') {
    throw new Error(`Missing translation: ${key}`)
  }

  return translation.replace(/:([a-z_]+)/g, (match, name) => replacements[name] ?? match)
}

export function registerTranslations(app) {
  app.config.globalProperties.$t = t
}
