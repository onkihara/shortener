# Integration und Verwendung der Vue 3 Komponenten

## 📋 Checkliste für die Integration

### 1. Vite-Konfiguration prüfen

Stelle sicher, dass Vite Vue 3 korrekt verarbeitet. Die Datei `vite.config.js` sollte die Vue-Erweiterung haben:

```javascript
import vue from '@vitejs/plugin-vue'

export default {
  plugins: [
    vue(),
  ],
}
```

### 2. Build-Prozess

```bash
# Komponenten für Development
npm run dev

# Komponenten für Production
npm run build
```

### 3. Import in Blade-Templates

Die Blade-Dateien wurden bereits aktualisiert:

- `resources/views/shortener/create.blade.php` → Nutzt `mountCreateForm()`
- `resources/views/shortener/dashboard.blade.php` → Nutzt `mountDashboard()`

## 🎯 Wie man neue Komponenten hinzufügt

### Beispiel: Neue Input-Komponente hinzufügen

**1. Komponente erstellen** (`resources/js/components/CreateForm/EmailInput.vue`):

```vue
<template>
  <div>
    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
      Email <span class="text-red-500">*</span>
    </label>
    <input
      id="email"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      type="email"
      placeholder="deine@email.com"
      class="w-full px-4 py-2 border border-gray-300 rounded-lg..."
      required
    >
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: String,
    required: true
  }
})

defineEmits(['update:modelValue'])
</script>
```

**2. In Parent-Komponente importieren und nutzen** (CreateForm.vue):

```vue
<script setup>
import EmailInput from './CreateForm/EmailInput.vue'
</script>

<template>
  <!-- ... andere Komponenten ... -->
  <EmailInput 
    v-model="form.email"
  />
</template>
```

## 🧪 Komponenten lokal testen

### Manueller Test in Browser

```vue
<!-- resources/views/test-component.blade.php -->
@extends('frame')

@section('main')
<div id="createFormApp"></div>

<script type="module">
    import { mountCreateForm } from '{{ asset('js/mounts/createFormMount.js') }}';
    
    document.addEventListener('DOMContentLoaded', () => {
        mountCreateForm('#createFormApp', {
            isAuthenticated: true
        });
    });
</script>
@endsection
```

Rufe auf: `yourapp.local/test-component`

### Mit Vue DevTools

1. Installiere Vue DevTools Browser-Extension
2. Öffne DevTools (F12)
3. Gehe zum Vue-Tab
4. Inspiziere Komponenten und ihren State

## 🔍 Debugging-Tipps

### Console-Logging in Komponenten

```vue
<script setup>
const handleSave = async () => {
  console.log('Form Data:', formData.value)
  console.log('Editing ID:', editingItem.value?.ID)
  // ...
}
</script>
```

### API-Calls debuggen

```javascript
// In Browser Console
// Alle Fetch-Calls loggen:
const originalFetch = window.fetch
window.fetch = function(...args) {
  console.log('Fetch:', args)
  return originalFetch(...args)
}
```

### Component DevTools

Nutze `<script setup>` - das ist besser für Debugging:
- Automatisches Ref-Unwrapping
- Bessere TypeScript-Unterstützung
- Leaner Code

## 📦 Abhängigkeiten

Die Komponenten benötigen nur:
- Vue 3 (`vue`: bereits installiert)
- Tailwind CSS (bereits konfiguriert)
- Laravel Blade (bereits vorhanden)

**Keine zusätzlichen Dependencies nötig!**

## 🚀 Performance-Tipps

### 1. Code-Splitting
Die Mount-Funktionen laden Komponenten nur on-demand:

```javascript
// Nur CreateForm wird geladen wenn create.blade.php aufgerufen wird
import { mountCreateForm } from './mounts/createFormMount.js'
```

### 2. Vite-Optimierungen

Prüfe `vite.config.js`:

```javascript
export default {
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          'vue': ['vue'],
          'create-form': ['./resources/js/mounts/createFormMount.js'],
          'dashboard': ['./resources/js/mounts/dashboardMount.js']
        }
      }
    }
  }
}
```

### 3. Bundle-Analyse

```bash
npm run build -- --analyze
```

## 🛠️ Häufige Probleme

### Problem: "Component not found"

**Lösung**: Stelle sicher, dass der Import-Pfad korrekt ist:
```javascript
// ✅ Richtig
import CreateForm from '../components/CreateForm.vue'

// ❌ Falsch
import CreateForm from './CreateForm'
```

### Problem: v-model funktioniert nicht

**Lösung**: Stelle sicher, dass beide `defineProps()` und `defineEmits()` richtig sind:

```vue
<script setup>
defineProps({
  modelValue: { type: String, required: true }
})

defineEmits(['update:modelValue'])
</script>

<!-- Template -->
<input
  :value="modelValue"
  @input="$emit('update:modelValue', $event.target.value)"
>
```

### Problem: Styles fehlen

**Lösung**: Prüfe dass Tailwind CSS richtig konfiguriert ist:
```bash
npm run dev
```

Tailwind sollte die `.vue` Dateien in `tailwind.config.js` erfassen:
```javascript
content: [
  './resources/views/**/*.{blade,php}',
  './resources/js/**/*.{js,vue}',  // ← Vue-Dateien inkludiert?
]
```

### Problem: CSRF Token fehlt

**Lösung**: Der Token wird automatisch aus Meta-Tag geholt:
```javascript
csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || ''
```

Stelle sicher dass in `resources/views/frame.blade.php` dieser Meta-Tag vorhanden ist:
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

## 📚 Weitere Ressourcen

- [Vue 3 Docs](https://vuejs.org/)
- [Vue 3 Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)
- [Vite Config](https://vitejs.dev/config/)
- [Tailwind CSS](https://tailwindcss.com/)

## ✨ Nächste Schritte

1. Teste beide Routes lokal:
   - `GET /shortener/create`
   - `GET /dashboard`

2. Verbesserungen:
   - Füge fehlende Validierungen hinzu
   - Implementiere Error Boundaries
   - Füge Loading-Skeletons hinzu
   - Optimiere API-Calls mit Caching

3. TypeScript Migration (optional):
   - Rename `.vue` zu `.vue` mit TypeScript
   - Nutze `<script setup lang="ts">`
   - Definiere Types für alle Props/Emits
