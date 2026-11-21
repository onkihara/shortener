# Vue 3 Komponenten-Struktur

Diese Dokumentation beschreibt die neuen Vue 3 Komponenten, die aus den ursprünglichen Blade-Dateien erstellt wurden.

## 📁 Verzeichnisstruktur

```
resources/
├── js/
│   ├── components/
│   │   ├── CreateForm.vue                 # Hauptkomponente für Formular
│   │   ├── Dashboard.vue                  # Hauptkomponente für Dashboard
│   │   ├── CreateForm/                    # Sub-Komponenten für Create-Form
│   │   │   ├── ErrorAlert.vue
│   │   │   ├── SuccessAlert.vue
│   │   │   ├── UrlInput.vue
│   │   │   ├── HashInput.vue
│   │   │   ├── DescriptionInput.vue
│   │   │   ├── PublishDateInput.vue
│   │   │   ├── ExpirationDateInput.vue
│   │   │   ├── SubmitButton.vue
│   │   │   └── AuthCallout.vue
│   │   └── Dashboard/                     # Sub-Komponenten für Dashboard
│   │       ├── DashboardHeader.vue
│   │       ├── CreateButtonBar.vue
│   │       ├── ErrorAlert.vue
│   │       ├── SuccessAlert.vue
│   │       ├── SearchBar.vue
│   │       ├── LoadingSpinner.vue
│   │       ├── ShortenerTable.vue
│   │       ├── ShortenerRow.vue
│   │       ├── EmptyState.vue
│   │       ├── Pagination.vue
│   │       ├── EditModal.vue
│   │       └── DeleteModal.vue
│   └── mounts/                            # Mount-Funktionen (kein globales Setup)
│       ├── createFormMount.js
│       └── dashboardMount.js
└── views/
    └── shortener/
        ├── create.blade.php               # Aktualisiert: nutzt Vue
        └── dashboard.blade.php            # Aktualisiert: nutzt Vue
```

## 🎯 Hauptmerkmale

### 1. **Keine globalen Importe**
- Jede Komponente wird nur dort importiert, wo sie tatsächlich benötigt wird
- Mount-Funktionen sind isoliert und werden nur in den Blade-Templates aufgerufen
- Keine globale App-Registrierung von Komponenten

### 2. **Kleingliedrige Komponenten**
Jede Komponente hat eine einzige Verantwortung:

#### CreateForm-Komponenten:
- **UrlInput.vue**: Eingabefeld für URLs
- **HashInput.vue**: Optionales Feld für benutzerdefinierten Hash (nur für authentifizierte Benutzer)
- **DescriptionInput.vue**: Textarea mit Zeichenzähler
- **PublishDateInput.vue**: Datepicker für Veröffentlichungsdatum
- **ExpirationDateInput.vue**: Datepicker für Ablaufdatum
- **SubmitButton.vue**: Submit-Button mit Ladestate
- **ErrorAlert.vue**: Fehlerbenachrichtigung
- **SuccessAlert.vue**: Erfolgsmeldung mit Copy-Funktion
- **AuthCallout.vue**: Call-to-Action für authentifizierte/nicht authentifizierte Benutzer

#### Dashboard-Komponenten:
- **DashboardHeader.vue**: Header mit Titel
- **CreateButtonBar.vue**: Button zum Erstellen neuer Links
- **SearchBar.vue**: Suchfeld mit Debouncing
- **LoadingSpinner.vue**: Lade-Animation
- **ShortenerTable.vue**: Tabelle für Links
- **ShortenerRow.vue**: Einzelne Tabellenzeile
- **EmptyState.vue**: Nachricht wenn keine Links vorhanden
- **Pagination.vue**: Navigations-Komponente
- **EditModal.vue**: Modal zum Bearbeiten von Links
- **DeleteModal.vue**: Bestätigungs-Modal zum Löschen
- **ErrorAlert.vue**: Fehlerbenachrichtigung
- **SuccessAlert.vue**: Erfolgsmeldung

### 3. **Mount-Funktionen (Lazy Loading)**
Die Mount-Funktionen befinden sich in `resources/js/mounts/`:

```javascript
// createFormMount.js
export function mountCreateForm(selector, props = {}) {
  const container = document.querySelector(selector)
  const app = createApp(CreateForm, {
    csrfToken: ...,
    isAuthenticated: ...,
    hashPattern: ...
  })
  app.mount(selector)
  return app
}
```

Verwendung in Blade:
```blade
<div id="createFormApp"></div>

<script type="module">
    import { mountCreateForm } from '{{ asset('js/mounts/createFormMount.js') }}';
    
    document.addEventListener('DOMContentLoaded', () => {
        mountCreateForm('#createFormApp', {
            isAuthenticated: {{ Auth::check() ? 'true' : 'false' }},
            hashPattern: '{{ config('shortener.hash.allowed') }}'
        });
    });
</script>
```

## 📊 Datenfluss

### CreateForm
```
CreateForm.vue (State-Management)
├── errorMessage, successMessage
├── form (url, hash, description, publish_date, expiration_date)
├── isSubmitting, charCount
└── submitForm() → API POST /api/shorten

Sub-Komponenten:
├── UrlInput ─ v-model: form.url
├── HashInput ─ v-model: form.hash
├── DescriptionInput ─ v-model: form.description + emit charCount
├── PublishDateInput ─ v-model: form.publish_date
├── ExpirationDateInput ─ v-model: form.expiration_date
├── SubmitButton ─ receive isSubmitting
├── ErrorAlert ─ receive errorMessage
└── SuccessAlert ─ receive successMessage + shortLink
    └── AuthCallout ─ receive isAuthenticated
```

### Dashboard
```
Dashboard.vue (State-Management)
├── shorteners[], searchQuery, currentPage
├── pagination, errorMessage, successMessage
├── isLoading, editingItem, deletingItem
├── loadShorteners() → API GET /api/shorteners
├── saveChanges() → API PATCH /api/shorteners/{id}
└── confirmDelete() → API DELETE /api/shorteners/{id}

Sub-Komponenten:
├── DashboardHeader
├── CreateButtonBar
├── SearchBar ─ v-model: searchQuery
├── LoadingSpinner ─ v-if: isLoading
├── ShortenerTable ─ :shorteners + emit edit/delete
│   └── ShortenerRow (v-for) ─ emit edit/delete
├── EmptyState ─ v-if: !shorteners.length
├── Pagination ─ :pagination + emit go-to-page
├── EditModal ─ :item + emit save/close
└── DeleteModal ─ :item + emit confirm/close
```

## 🔄 Reaktive Updates

### CreateForm
- **v-model**: Zwei-Wege-Bindung für alle Eingabefelder
- **emit**: Bestätigung von Zeichenzahl in DescriptionInput
- **computed**: Automatische Zeichenzähler-Berechnung

### Dashboard
- **API-Polling**: loadShorteners() lädt Daten vom Server
- **Debouncing**: Suchfeld mit 300ms Verzögerung
- **Modal-Management**: editingItem und deletingItem sind null, wenn Modals geschlossen
- **Pagination**: Automatisches Laden beim Seitenwechsel

## 💡 Best Practices

1. **Props validieren**: Alle Komponenten nutzen `defineProps()` mit Typdeklarationen
2. **Events emittieren**: `defineEmits()` explizit definiert
3. **Keine Slots nötig**: Komponenten sind spezialisiert genug
4. **CSS/Tailwind**: Alle Styles sind in den Komponenten, keine globalen Abhängigkeiten
5. **Fehlerbehandlung**: Try-catch in allen API-Aufrufen
6. **User Feedback**: Loading-States, Error/Success-Messages

## 🚀 Verwendung

### In Blade-Templates

**CreateForm:**
```blade
<div id="createFormApp"></div>

<script type="module">
    import { mountCreateForm } from '{{ asset('js/mounts/createFormMount.js') }}';
    
    document.addEventListener('DOMContentLoaded', () => {
        mountCreateForm('#createFormApp', {
            isAuthenticated: {{ Auth::check() ? 'true' : 'false' }}
        });
    });
</script>
```

**Dashboard:**
```blade
<div id="dashboardApp"></div>

<script type="module">
    import { mountDashboard } from '{{ asset('js/mounts/dashboardMount.js') }}';
    
    document.addEventListener('DOMContentLoaded', () => {
        mountDashboard('#dashboardApp', {
            timezone: '{{ config('app.timezone') }}'
        });
    });
</script>
```

## 📝 Wichtige Unterschiede zur Original-Blade

| Aspekt | Original Blade | Vue 3 |
|--------|---|---|
| Globales Setup | Globale Funktionen | Isolierte Mount-Funktionen |
| Komponenten | Monolithisches HTML | Kleine fokussierte Komponenten |
| State | DOM-basiert (jQuery-ähnlich) | Vue Reactivity System |
| Wiederverwendbarkeit | Schwer zu testen | Jede Komponente isoliert testbar |
| Bundle-Größe | Groß bei viel JS | Kann mit Tree-Shaking optimiert werden |

## 🔧 Zukünftige Verbesserungen

1. **Composables**: Logik in separate `useShorteners.js` auslagern
2. **Stores**: State Management mit Pinia/reactive()
3. **Testing**: Jede Komponente mit Unit-Tests
4. **Vite Config**: Optimiertes Bundling für Mount-Funktionen
5. **TypeScript**: Vollständige Typ-Sicherheit
