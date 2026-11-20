# Shortener-App - Implementierungsplan

## 📋 Projektübersicht
Eine URL-Shortener-Anwendung mit Hash-Generierung, Verfolgung von Aufrufen und Benutzerverwaltung.

## Hash-Konstruktion
Für die Hash-Konstruktion wird eine möglichst kurze (nicht kürzer als min-length) und nicht länger als max-length zufällige Auswahl aus den Buchstaben des character-strings in config('shortener.hash') benutzen.
Bei schon existierenden Hashes wird der Versuch try-times wiederholt.
---

## 🏗️ Technische Grundlage (vorhanden)

### Datenbank-Schema
- **Tabelle**: `shortcuts`
- **Spalten**:
  - `ID` (Primary Key, auto-increment)
  - `UserID` (nullable - anonym oder angemeldet)
  - `Hash` (unique, max 16 Zeichen)
  - `Url` (max 1024 Zeichen)
  - `Description` (max 1024 Zeichen, optional)
  - `Frequency` (Aufrufe, default: 0)
  - `CreationDate`, `UpdateDate` (Timestamps)
  - `PublishDate` (Veröffentlichungsdatum, optional - künftige Verfügbarkeit)
  - `DeleteDate` (Soft-Delete)

### Models & Controller
- **Model**: `App\Model\Shortener` (mit Soft Deletes)
- **Controller**: `ShortenerController`
- **User-Relation**: One-to-Many (User → Shortener)

### Routes (API-fokussiert)
- `GET /{hash}` - Redirect zur URL + Frequency erhöhen
- `GET /shorten` - Shortener-Liste abrufen (Auth)
- `PUT /shorten` - Neue Shortener erstellen (Auth)
- `DELETE /shorten` - Shortener löschen (Auth)

---

## 🎯 Implementierungs-Phasen

### Phase 1: Backend-Vorbereitung
- [ ] **1.1** Config-Datei `config/shortener.php` erstellen
  - Hash-Format-Optionen
  - Hash-Längen und Retrials
  - Case (uppercase/lowercase)
  
- [ ] **1.2** ShortenerController vervollständigen
  - `get()` - Existierende Shortener des Users auflisten mit Pagination
  - `shorten()` - Neue Shortener erstellen (mit oder ohne Hash-Wunsch)
  - `delete()` - Soft-Delete und Hard-Delete
  - Validierung für URLs und Hash-Namen
  - Response-Format konsistenter machen (JSON-API)

- [ ] **1.3** Shortener Model erweitern
  - Fillable-Array komplettieren (Description hinzufügen)
  - Accessors für Hash-Generierung
  - Scopes für Filtering (z.B. `active()`, `byUser()`)
  - Timestamps korrekt konfigurieren

- [ ] **1.4** API-Routes verfeinern
  - Public Route für Redirect mit Hash
  - Auth-geschützte Routes für CRUD
  - Error-Handling verbessern
  - Query-String-Parameter für Pagination/Filtering

---

### Phase 2: Frontend - Öffentliche Eingabemaske
- [ ] **2.1** Blade-Template erstellen: `resources/views/shortener/create.blade.php`
  - Form für URL-Input
  - Optionales Textfeld für custom Hash
  - Optionales Textfeld für Beschreibung
  - Submit-Button
  - Error-Meldungen anzeigen
  - Styling mit Tailwind CSS (oder bestehendes CSS-Framework)

- [ ] **2.2** JavaScript für API-Kommunikation
  - Form-Submission via AJAX
  - Validierung auf Client-Side
  - Erfolgs-Meldung mit generiertem Short-Link
  - Copy-to-Clipboard-Funktion
  - Loading-State

- [ ] **2.3** Route für Frontend
  - `GET /` oder `GET /create` - Öffentliche Eingabemaske anzeigen
  - `POST /api/shorten` - Shortener erstellen (optional auch ohne Auth)

---

### Phase 3: Frontend - Benutzer-Dashboard (Auth)
- [ ] **3.1** Blade-Template für Dashboard: `resources/views/shortener/dashboard.blade.php`
  - Tabelle/Grid mit User-Shortcuts:
    - Hash (mit Link)
    - Original-URL
    - Beschreibung
    - Häufigkeit (Frequency)
    - Erstellung (CreationDate)
    - Aktionen: Edit, Delete
  - Pagination mit Links
  - Optionaler Filter/Suche

- [ ] **3.2** Edit-View: `resources/views/shortener/edit.blade.php`
  - Bearbeitbar: Description
  - Hash kann nicht geändert werden (aber angezeigt)
  - URL kann nicht geändert werden (aber angezeigt)
  - Save/Cancel-Buttons
  - Validation-Feedback

- [ ] **3.3** Auth-geschützte Routes
  - `GET /dashboard` - Dashboard anzeigen
  - `GET /shortener/{id}/edit` - Edit-Form
  - `POST /shortener/{id}` - Speichern
  - `DELETE /shortener/{id}` - Löschen mit Bestätigung

- [ ] **3.4** JavaScript für Dashboard
  - Delete-Bestätigung (Modal/Dialog)
  - Inline-Editing (optional)
  - Copy-to-Clipboard für Short-Links

---

### Phase 4: Zusätzliche Features
- [ ] **4.1** Exception Handling
  - `App\Exceptions\ShortenerException` ist vorhanden
  - Custom Error-Seite für ungültige Hashes
  - API-Error-Responses standardisieren

- [ ] **4.2** Validierung
  - URL-Validierung (gültige URLs nur)
  - Hash-Validierung (erlaubte Zeichen: a-z, 0-9, Bindestrich)
  - Duplikat-Check für Custom-Hashes

- [ ] **4.3** Authentifizierung
  - Existiert bereits (Laravel Auth)
  - Sicherstellen, dass nur Besitzer ihre Shortcuts editieren/löschen kann
  - Policy für Shortener erstellen

- [ ] **4.4** Testing
  - Feature-Tests für API-Routes
  - Unit-Tests für Validierung und Hash-Generierung
  - Browser-Tests für Frontend-Interaktionen

---

## 📁 Datei-Struktur (zu erstellen/anpassen)

```
shortener/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ShortenerController.php ✏️
│   │   └── Requests/
│   │       ├── StoreShortenerRequest.php ✨ (NEU)
│   │       └── UpdateShortenerRequest.php ✨ (NEU)
│   ├── Models/
│   │   ├── Shortener.php ✏️
│   │   └── User.php
│   ├── Policies/
│   │   └── ShortenerPolicy.php ✨ (NEU)
│   └── Exceptions/
│       └── ShortenerException.php (existiert)
├── config/
│   └── shortener.php ✨ (NEU)
├── database/
│   └── migrations/
│       └── 2018_09_19_125507_create_shortcut_table.php ✓
├── resources/
│   ├── views/
│   │   └── shortener/
│   │       ├── create.blade.php ✨ (NEU)
│   │       ├── dashboard.blade.php ✨ (NEU)
│   │       └── edit.blade.php ✨ (NEU)
│   └── js/
│       └── shortener/ ✨ (NEU)
│           ├── form.js
│           └── dashboard.js
├── routes/
│   └── web.php ✏️
└── tests/
    ├── Feature/
    │   └── ShortenerTest.php ✨ (NEU)
    └── Unit/
        └── ShortenerModelTest.php ✨ (NEU)
```

**Legende**: ✓ existiert | ✏️ zu ändern | ✨ zu erstellen

---

## 🔄 Workflow-Flow

### Anonymer Benutzer
1. Öffnet `/` oder `/create`
2. Füllt URL-Formular aus (optional: Hash, Beschreibung)
3. Klick auf "Shortener erstellen"
4. AJAX-Request an `/api/shorten` (PUBLIC)
5. Erhält Hash zurück
6. Short-Link wird angezeigt und kann kopiert werden

### Angemeldeter Benutzer
1. **Shortener erstellen**: Wie anonymer Benutzer (aber mit `UserID` in DB)
2. **Dashboard aufrufen**: Klick auf `/dashboard`
3. **Shortener ansehen**:
   - Tabelle mit eigenen Shortcuts
   - Zeigt Hash, URL, Häufigkeit, Datum
4. **Shortener editieren**: Klick auf "Edit" → Änderung der Beschreibung → Speichern
5. **Shortener löschen**: Klick auf "Delete" → Bestätigung → Soft-Delete

---

## 🔐 Sicherheit & Best Practices

- [ ] CSRF-Protection in Forms (Laravel default)
- [ ] Authorization Policy: Nur Besitzer kann editieren/löschen
- [ ] URL-Validierung gegen Malware/Phishing (optional: externe Service)
- [ ] Rate-Limiting für API-Requests (DoS-Schutz)
- [ ] Input-Sanitization
- [ ] SQL-Injection-Schutz (Eloquent ORM schützt bereits)
- [ ] XSS-Schutz (Blade escaping)

---

## 📊 Statistiken & Monitoring

- Häufigkeit (Frequency) wird bei jedem Redirect erhöht ✓
- Tracking von Erstellungs- und Änderungsdatum ✓
- Optionale Erweiterung: Logs mit IP, User-Agent, Referrer

---

## ✅ Validierungs-Regeln

| Feld | Regel | Fehlermeldung |
|------|-------|---------------|
| URL | Required, Valid URL, Max 1024 | "Gültige URL erforderlich" |
| Hash | Optional, Slug, Unique, Max 16 | "Hash bereits vergeben" |
| Description | Optional, Max 1024 | "Max 1024 Zeichen" |

---

## 🎨 UI/UX Considerations

- **Öffentliche Form**: Minimal, schnell, verständlich
- **Dashboard**: Übersichtlich, sortierbar, responsive
- **Meldungen**: Toast/Alert für Erfolg/Fehler
- **Mobile**: Responsive Design (Tailwind CSS)
- **Copy-Button**: One-Click Kopieren in Zwischenablage

---

## 📝 Nächste Schritte

1. **Zuerst**: Config-Datei erstellen + Model/Controller vervollständigen
2. **Dann**: Backend-Tests schreiben
3. **Danach**: Frontend-Views + JavaScript
4. **Abschließend**: E2E-Tests + Production-Ready
