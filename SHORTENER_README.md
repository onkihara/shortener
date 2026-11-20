# 🔗 Shortener-App

Eine moderne, sichere und benutzerfreundliche URL-Shortener-Anwendung basierend auf Laravel 13.

**Live in Action:** Erstelle Short-Links für lange URLs ohne Anmeldung. Angemeldete Benutzer können ihre Links verwalten, bearbeiten und die Häufigkeit der Aufrufe verfolgen.

---

## ✨ Features

### 🌐 Öffentliche Features
- ✅ URL zu Short-Link konvertieren (ohne Anmeldung)
- ✅ Optional: Custom Hash eingeben
- ✅ Optional: Beschreibung hinzufügen
- ✅ Automatische Generierung eindeutiger Hashes (6-8 Zeichen)
- ✅ Redirect mit Frequency-Tracking
- ✅ Responsive Design (Mobile-ready)

### 👤 Authentifizierte Features
- ✅ Dashboard mit eigenen Short-Links
- ✅ Pagination und Search/Filter
- ✅ Häufigkeit der Aufrufe anzeigen (📊)
- ✅ Beschreibung bearbeiten
- ✅ Links löschen (Soft-Delete)
- ✅ Copy-to-Clipboard-Funktion

### 🔐 Sicherheit & Quality
- ✅ CSRF-Protection
- ✅ Authorization Policy (nur Owner kann editieren)
- ✅ Input-Validierung
- ✅ Rate-Limiting (30 req/min)
- ✅ Soft-Delete Support
- ✅ SQL-Injection-Proof (Eloquent ORM)
- ✅ XSS-Protection (Blade Escaping)

---

## 🛠️ Tech Stack

- **Backend**: Laravel 13 (PHP 8.3+)
- **Frontend**: Blade Templates + Vanilla JavaScript
- **Styling**: Tailwind CSS
- **Database**: MySQL/PostgreSQL/SQLite
- **Testing**: Pest (PHPUnit compatible)
- **Code Quality**: Laravel Pint (PSR-12)

---

## 📦 Installation

### 1. Abhängigkeiten installieren
```bash
composer install
npm install
```

### 2. Environment konfigurieren
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Datenbank-Verbindung in `.env` setzen
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shortener
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Datenbank-Migrationen
```bash
php artisan migrate
```

### 5. Frontend Assets kompilieren
```bash
npm run build
# oder für Development
npm run dev
```

---

## 🚀 Usage

### Development starten
```bash
php artisan serve
# App verfügbar unter http://localhost:8000
```

### URLs im Browser
- **Öffentliche Eingabemaske**: `http://localhost:8000/`
- **Dashboard** (Auth nötig): `http://localhost:8000/dashboard`
- **Shortener aufrufen**: `http://localhost:8000/{hash}`

---

## 📚 API-Dokumentation

Alle API-Responses sind JSON-formatiert.

### Public Endpoints

#### POST /api/shorten
Erstellt einen neuen Shortener

**Request:**
```json
{
  "url": "https://example.com/very/long/url",
  "hash": "optional-custom-hash",
  "description": "optional description"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Shortener erfolgreich erstellt.",
  "data": {
    "id": 1,
    "hash": "abc123",
    "url": "https://example.com/very/long/url",
    "description": "optional description",
    "short_link": "http://localhost:8000/abc123"
  }
}
```

#### GET /{hash}
Redirect zur Full-URL (301 Permanent)

#### GET /api/shorten?hash={hash}
Shortener-Daten ohne Redirect

---

### Protected Endpoints (Require Authentication)

#### GET /api/shorteners
Shortener-Liste mit Pagination
```
?page=1
?search=keyword
```

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "ID": 1,
      "Hash": "abc123",
      "Url": "https://example.com",
      "Description": "My link",
      "Frequency": 42,
      "CreationDate": "2024-11-20T12:00:00.000000Z"
    }
  ],
  "pagination": {
    "total": 10,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

#### PATCH /api/shorteners/{id}
Shortener-Beschreibung aktualisieren

**Request:**
```json
{
  "description": "New description"
}
```

#### DELETE /api/shorteners/{id}
Shortener löschen (Soft-Delete)
```
?hard=1  // für Hard-Delete
```

---

## 🧪 Testing

```bash
# Alle Tests ausführen
php artisan test

# Nur Unit Tests
php artisan test tests/Unit

# Nur Feature Tests
php artisan test tests/Feature

# Mit Coverage
php artisan test --coverage

# Einzelnen Test ausführen
php artisan test tests/Feature/ShortenerTest.php --filter=creates_shortener_with_valid_url
```

**Test Coverage:**
- ✅ 25+ Pest Test Cases
- ✅ Model & Validation Tests
- ✅ API Endpoint Tests
- ✅ Authorization & Security Tests
- ✅ Edge-Case Tests

---

## 📂 Project Structure

```
shortener/
├── app/
│   ├── Http/
│   │   ├── Controllers/ShortenerController.php
│   │   ├── Requests/
│   │   │   ├── StoreShortenerRequest.php
│   │   │   └── UpdateShortenerRequest.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Shortener.php
│   │   └── User.php
│   ├── Policies/
│   │   └── ShortenerPolicy.php
│   └── Exceptions/
│       └── ShortenerException.php
├── config/
│   └── shortener.php
├── database/
│   ├── factories/ShortenerFactory.php
│   └── migrations/
├── resources/
│   ├── views/
│   │   └── shortener/
│   │       ├── create.blade.php (Öffentliche Form)
│   │       ├── dashboard.blade.php (User Dashboard)
│   │       └── edit.blade.php (Edit Modal)
│   └── js/
├── routes/
│   └── web.php
├── tests/
│   ├── Feature/ShortenerTest.php
│   └── Unit/ShortenerModelTest.php
├── SHORTENER_APP_PLAN.md (Implementierungsplan)
├── IMPLEMENTATION_STATUS.md (Status & Checkliste)
└── QUICK_START.md (Quick-Start Guide)
```

---

## ⚙️ Konfiguration

Die Shortener-Konfiguration befindet sich in `config/shortener.php`:

```php
'hash' => [
    'lengths' => [6, 7, 8],      // Hash-Längen
    'retrials' => 10,             // Versuche pro Länge
    'characters' => '...',        // Erlaubte Zeichen
    'max_length' => 16,           // Max. Hash-Länge
],
'url' => [
    'max_length' => 1024,         // Max. URL-Länge
    'allowed_schemes' => [...]    // Erlaubte Protokolle
],
'pagination' => [
    'per_page' => 15,             // Items pro Seite
]
```

---

## 🔐 Authentifizierung

Die App verwendet Laravel's Standard-Authentication (Session-based):

```bash
# User registrieren und anmelden
http://localhost:8000/register
http://localhost:8000/login
```

Protected Routes sind auf `auth` Middleware konfiguriert.

---

## 🌍 Deployment

### Zu Laravel Cloud
```bash
# Mit Laravel Cloud CLI
cloud deploy
```

### Zu traditionellem Server
```bash
# 1. Code hochladen
git push origin main

# 2. SSH auf Server
ssh user@server

# 3. Dependencies installieren
composer install --no-dev --optimize-autoloader

# 4. Environment konfigurieren
cp .env.example .env
# Editiere .env mit korrekten Werten

# 5. App Key generieren
php artisan key:generate

# 6. Cache prewarmen
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Datenbank-Migrationen
php artisan migrate --force

# 8. Assets kompilieren
npm run build

# 9. Web-Server konfigurieren (nginx/Apache)
# Document Root: /path/to/shortener/public
```

---

## 🐛 Troubleshooting

### "CSRF Token Mismatch"
- Stellen Sie sicher, dass JavaScript CSRF-Token sendet
- Blade Form mit `@csrf` verwenden

### "Hash already taken"
- Wählen Sie einen anderen custom Hash
- Oder lassen Sie einen generieren (default)

### "Shortener nicht gefunden"
- Überprüfen Sie, ob der Hash korrekt ist
- Prüfen Sie PublishDate (nicht veröffentlicht?)

### Tests schlagen fehl
```bash
php artisan migrate:fresh
php artisan test
```

---

## 📄 Lizenzen

Dieses Projekt ist unter der MIT-Lizenz lizenziert.

---

## 👨‍💻 Development

Fragen oder Verbesserungsvorschläge?

- 📧 Erstelle einen Issue
- 🔧 Reiche einen Pull Request ein
- 💬 Öffne eine Discussion

---

## 📖 Weitere Ressourcen

- [Laravel Dokumentation](https://laravel.com/docs)
- [Pest Testing Dokumentation](https://pestphp.com)
- [Tailwind CSS Dokumentation](https://tailwindcss.com)
- [SHORTENER_APP_PLAN.md](./SHORTENER_APP_PLAN.md) - Detaillierter Plan
- [QUICK_START.md](./QUICK_START.md) - Quick-Start Guide

---

**Happy Shortening! 🔗**
