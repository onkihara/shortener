# URL Shortener - Laravel Anwendung

Ein modernes, sicheres URL-Verkürzer-System mit erweiterten Features wie QR-Code-Generierung, Veröffentlichungsplanung, Ablaufdatum und YouTube-URL-Konvertierung.

## 📋 Inhaltsverzeichnis

- [Funktionen](#funktionen)
- [Technischer Aufbau](#technischer-aufbau)
- [Anforderungen](#anforderungen)
- [Installation](#installation)
- [Konfiguration](#konfiguration)
- [Verwendung](#verwendung)
- [API-Endpoints](#api-endpoints)
- [Entwicklung](#entwicklung)
- [Testen](#testen)
- [Deployment](#deployment)
- [Lizenz](#lizenz)

## ✨ Funktionen

### Kern-Funktionalität

- **URL-Verkürzung**: Konvertiert lange URLs in kurze, leicht merkbare Links
- **Eindeutige Hash-Generierung**: Automatische Generierung von eindeutigen, kurzen Hashes
- **Custom Hash**: Benutzer können eigene Custom Hashes definieren (z.B. `example.com/mein-link`)
- **Zugriffszähler**: Verfolgung der Häufigkeit, wie oft ein Shortlink verwendet wurde
- **Soft Deletes**: Gelöschte Links bleiben in der Datenbank erhalten (können wiederhergestellt werden)

### Erweiterte Features

#### Veröffentlichungsplanung
- **Publish Date**: Shortlinks können zeitgesteuert veröffentlicht werden
- Shortlinks sind erst nach dem Veröffentlichungsdatum aktiv
- Perfekt für zeitgesteuerte Kampagnen und Ankündigungen

#### Ablaufdatum
- **Expiration Date**: Links können mit einem Ablaufdatum versehen werden
- Automatische Deaktivierung nach dem Ablaufdatum
- Ideal für temporäre Promotionen und zeitlich begrenzte Inhalte

#### Metadaten
- **Beschreibung**: Optionale Beschreibung für jeden Shortlink
- Bessere Verwaltung und Dokumentation der Links

#### QR-Code-Generierung
- Automatische Erstellung von QR-Codes für jeden Shortlink
- Integration mit der `endroid/qr-code` Bibliothek
- Einfache Mobile-Nutzung

#### YouTube-URL-Konvertierung
- Automatische Erkennung von YouTube-URLs
- Konvertierung zu werbefreien Embed-Varianten
- Verbessertes Nutzererlebnis ohne störende Werbung

#### Social Login (OAuth)
- **Blikk OAuth**: Integration mit Blikk-Authentifizierung
- Sichere Benutzer-Authentifizierung ohne Passwort-Verwaltung
- Einmalige Anmeldung (Single Sign-On)

#### Multi-Language Support
- **Lokalisierung**: Mehrsprachige Benutzeroberfläche
- Sprache kann via POST-Request gewechselt werden
- Speicherung in Session

#### Dashboard
- **Authentifizierte Benutzer**: Dashboard für registrierte Nutzer
- **Shortlink-Management**: Erstellen, Bearbeiten, Löschen von Links
- **Statistiken**: Übersicht über die Zugriffe auf Ihre Links

## 🏗️ Technischer Aufbau

### Stack

| Komponente | Version | Beschreibung |
|-----------|---------|-------------|
| **PHP** | 8.3+ | Programmiersprache |
| **Laravel Framework** | 13.17+ | Web-Framework |
| **Laravel Socialite** | 5.33+ | OAuth-Integration |
| **Vue.js** | 3.5+ | Frontend-Framework |
| **Tailwind CSS** | 4.0+ | CSS-Framework |
| **Vite** | 8.0+ | Frontend-Build-Tool |
| **Pest** | 5.3+ | PHP Testing-Framework |
| **Laravel Pint** | 1.27+ | PHP Code Formatter |

### Abhängigkeiten

#### Production Dependencies
- **laravel/framework**: Basis-Framework
- **laravel/socialite**: OAuth/Social Login
- **laravel/tinker**: REPL für Laravel
- **endroid/qr-code**: QR-Code-Generierung
- **onkihara/socialite_blikk**: Custom Blikk OAuth Provider

#### Development Dependencies
- **pestphp/pest**: Testing-Framework
- **laravel/pint**: Code-Formatter
- **laravel/pail**: Log Viewer
- **laravel/boost**: Development Tools
- **mockery/mockery**: Mocking-Framework

### Datenbankschema

#### Tabelle: `shortcuts`

| Spalte | Typ | Beschreibung |
|--------|-----|-------------|
| `ID` | INT | Primärschlüssel (auto-increment) |
| `UserID` | INT | Benutzer-ID (Fremdschlüssel) |
| `Hash` | VARCHAR(16) | Eindeutiger Shortlink-Hash |
| `Url` | VARCHAR(1024) | Ziel-URL |
| `Description` | VARCHAR(1024) | Optionale Beschreibung |
| `Frequency` | INT | Zugriffszähler |
| `CreationDate` | DATETIME | Erstellungsdatum |
| `UpdateDate` | DATETIME | Letztes Änderungsdatum |
| `PublishDate` | DATETIME | Veröffentlichungsdatum (optional) |
| `ExpirationDate` | DATETIME | Ablaufdatum (optional) |
| `DeleteDate` | DATETIME | Löschdatum (soft delete) |

### Modelle

#### Shortener Model
- Repräsentiert einen Shortlink
- Custom Timestamps (`CreationDate`, `UpdateDate`, `DeleteDate`)
- Scopes: `active()`, `published()`, `byUser()`
- Methoden: `generateHash()`, `isPublished()`, `isExpired()`, `recordAccess()`

#### User Model
- Repräsentiert einen Benutzer
- Authentifizierung via OAuth/Socialite
- Beziehung zu Shortener-Links (1:N)

### Verzeichnisstruktur

```
shortener/
├── app/
│   ├── Models/              # Eloquent Models
│   │   ├── Shortener.php
│   │   └── User.php
│   ├── Http/
│   │   ├── Controllers/     # API & Web Controller
│   │   │   ├── ShortenerController.php
│   │   │   └── UserController.php
│   │   ├── Requests/        # Form Request Validation
│   │   │   ├── StoreShortenerRequest.php
│   │   │   └── UpdateShortenerRequest.php
│   │   └── Middleware/      # Custom Middleware
│   │       └── SetLocale.php
│   ├── Services/            # Service Layer
│   │   ├── QrCodeGenerator.php
│   │   └── YouTubeConverter.php
│   ├── Policies/            # Authorization Policies
│   │   └── ShortenerPolicy.php
│   └── Providers/           # Service Provider
│       └── AppServiceProvider.php
├── routes/
│   ├── web.php              # Web Routes
│   └── api.php              # API Routes
├── resources/
│   ├── views/               # Blade Templates
│   └── js/                  # Vue Components
├── config/
│   ├── shortener.php        # App Configuration
│   └── ...
├── database/
│   ├── migrations/          # Datenbank-Migrationen
│   ├── factories/           # Model Factories
│   └── seeders/             # Database Seeder
├── tests/
│   ├── Feature/             # Feature Tests
│   ├── Unit/                # Unit Tests
│   └── Pest.php
└── storage/
    └── app/                 # Lokale Dateiablage
```

### Services

#### QrCodeGenerator Service
- Generiert QR-Codes für Shortlinks
- Verwendet `endroid/qr-code` Bibliothek
- Customisierbare Größe und Format

#### YouTubeConverter Service
- Erkennt YouTube-URLs
- Konvertiert zu werbefreien Embed-URLs
- Unterstützt verschiedene YouTube-URL-Formate

## 📦 Anforderungen

### Systemanforderungen

- **PHP**: 8.3 oder höher
- **Composer**: 2.0+
- **Node.js**: 18+
- **npm**: 9+
- **Datenbank**: SQLite, MySQL oder PostgreSQL

### PHP Extensions

- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- Filter
- Hash
- Mbstring
- OpenSSL
- PCRE
- PDO
- Tokenizer
- XML

## 🚀 Installation

### 1. Repository klonen

```bash
git clone https://github.com/onkihara/shortener.git
cd shortener
```

### 2. Automatische Setup (empfohlen)

```bash
composer run setup
```

Dieser Befehl führt folgendes aus:
- Installiert PHP-Dependencies mit Composer
- Kopiert `.env.example` zu `.env` (falls nicht vorhanden)
- Generiert Application Key
- Führt Datenbank-Migrationen aus
- Installiert JavaScript-Dependencies
- Kompiliert Frontend-Assets

### 3. Manuelle Installation

Falls das automatische Setup nicht funktioniert:

#### 3.1 PHP Dependencies installieren
```bash
composer install
```

#### 3.2 Environment-Datei erstellen
```bash
cp .env.example .env
```

#### 3.3 Application Key generieren
```bash
php artisan key:generate
```

#### 3.4 Datenbank-Migrationen
```bash
# Mit SQLite
php artisan migrate

# Oder mit anderen Datenbanken (siehe Konfiguration)
php artisan migrate --database=mysql
```

#### 3.5 JavaScript Dependencies
```bash
npm install
```

#### 3.6 Frontend Assets kompilieren
```bash
npm run build
```

### 4. Entwicklungsserver starten

```bash
composer run dev
```

Dieser Befehl startet:
- Laravel Development Server (Port 8000)
- Vite Development Server (Port 5173)
- Asset Compilation Watcher

Zugriff: `http://localhost:8000`

## ⚙️ Konfiguration

### Environment-Variablen (.env)

#### Basis-Konfiguration
```env
APP_NAME=Shortener
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Sprach-Einstellungen
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

#### Datenbank
```env
# SQLite (Standard)
DB_CONNECTION=sqlite

# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shortener
DB_USERNAME=root
DB_PASSWORD=

# PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=shortener
DB_USERNAME=postgres
DB_PASSWORD=
```

#### Session
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
```

#### Cache & Queue
```env
CACHE_STORE=database
QUEUE_CONNECTION=database
```

#### OAuth / Socialite (Blikk)
```env
BLIKK_CLIENT_ID=your_blikk_client_id
BLIKK_CLIENT_SECRET=your_blikk_client_secret
BLIKK_REDIRECT_URI=http://localhost:8000/oauth/callback
```

### Shortener-Konfiguration (config/shortener.php)

```php
return [
    'hash' => [
        // Verfügbare Hash-Längen (wird der Reihe nach versucht)
        'lengths' => [4, 5, 6, 7, 8],
        
        // Versuche pro Länge
        'retrials' => 10,
        
        // Erlaubte Zeichen
        'characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789',
    ]
];
```

## 💻 Verwendung

### Für Endbenutzer

#### Home-Seite
- Öffnen Sie `http://localhost:8000/`
- Nicht-authentifizierte Benutzer sehen die Landing Page

#### Anmeldung
- Klicken Sie auf "Login"
- Authentifizierung über Blikk OAuth
- Automatische Weiterleitung zum Shortlink-Erstellungs-Formular

#### Shortlink erstellen
- Navigieren Sie zu `/shortener/create`
- Ausfüllen des Formulars:
  - **URL** (erforderlich): Die zu verkürzende URL
  - **Custom Hash** (optional): Benutzerdefinierter Hash (z.B. "mein-link")
  - **Beschreibung** (optional): Notiz/Beschreibung
  - **Veröffentlichungsdatum** (optional): Wann der Link aktiv wird
  - **Ablaufdatum** (optional): Wann der Link inaktiv wird
- Shortlink wird generiert und angezeigt
- QR-Code wird automatisch erstellt

#### Dashboard
- Navigieren Sie zu `/shortener/dashboard`
- Übersicht aller Ihrer Shortlinks
- Statistiken (Zugriffszähler)
- Optionen zum Bearbeiten/Löschen

### Shortlink verwenden
- Teilen Sie den generierten Short-URL: `example.com/{hash}`
- Benutzer werden automatisch zur Ziel-URL weitergeleitet
- Zugriffszähler wird erhöht

## 🔌 API-Endpoints

### Authentifizierung
Alle API-Endpoints außer dem Redirect erfordern Authentication.

### Öffentliche Endpoints

#### GET `/{hash}`
Leitet zur ursprünglichen URL um.

**Parameter:**
- `hash` (string): Shortlink-Hash

**Responses:**
- `301`: Redirect zur Ziel-URL
- `409`: Shortlink noch nicht veröffentlicht
- `410`: Shortlink abgelaufen
- `404`: Hash nicht gefunden

**Beispiel:**
```bash
curl -L http://localhost:8000/abc123
```

---

### Authentifizierte Endpoints

#### GET `/api/shorteners`
Liste aller Shortlinks des aktuellen Benutzers.

**Query Parameter:**
- `page` (int): Seitennummer für Pagination
- `per_page` (int): Einträge pro Seite

**Response:**
```json
{
  "data": [
    {
      "ID": 1,
      "Hash": "abc123",
      "Url": "https://example.com/very/long/url",
      "Description": "My first short link",
      "Frequency": 42,
      "PublishDate": "2024-01-01T00:00:00",
      "ExpirationDate": null,
      "CreationDate": "2024-01-01T00:00:00",
      "UpdateDate": "2024-01-01T00:00:00"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "total": 1
  }
}
```

**Beispiel:**
```bash
curl -H "Authorization: Bearer {token}" \
  http://localhost:8000/api/shorteners?page=1&per_page=10
```

---

#### POST `/api/shorteners`
Erstellt einen neuen Shortlink.

**Body:**
```json
{
  "url": "https://example.com/very/long/url",
  "custom_hash": "mein-link",
  "description": "Meine Beschreibung",
  "publish_date": "2024-01-15T00:00:00",
  "expiration_date": "2025-01-15T00:00:00"
}
```

**Validierung:**
- `url` (erforderlich): Gültige URL
- `custom_hash` (optional): Eindeutiger, URL-sicherer String
- `description` (optional): Max. 1024 Zeichen
- `publish_date` (optional): Datetime string
- `expiration_date` (optional): Datetime string

**Response:**
```json
{
  "ID": 2,
  "Hash": "mein-link",
  "Url": "https://example.com/very/long/url",
  "Description": "Meine Beschreibung",
  "Frequency": 0,
  "PublishDate": "2024-01-15T00:00:00",
  "ExpirationDate": "2025-01-15T00:00:00",
  "CreationDate": "2024-01-10T12:00:00",
  "UpdateDate": "2024-01-10T12:00:00",
  "qr_code": "data:image/png;base64,..."
}
```

**Beispiel:**
```bash
curl -X POST http://localhost:8000/api/shorteners \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://example.com/very/long/url",
    "custom_hash": "mein-link",
    "description": "Test"
  }'
```

---

#### PUT `/api/shorteners/{id}`
Aktualisiert einen existierenden Shortlink.

**Body:** Gleich wie POST (alle Felder optional)

**Response:** Aktualisierter Shortlink (JSON)

**Beispiel:**
```bash
curl -X PUT http://localhost:8000/api/shorteners/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "description": "Updated description"
  }'
```

---

#### DELETE `/api/shorteners/{id}`
Löscht einen Shortlink (Soft Delete).

**Response:** 
```json
{
  "message": "Shortlink successfully deleted",
  "ID": 1
}
```

**Beispiel:**
```bash
curl -X DELETE http://localhost:8000/api/shorteners/1 \
  -H "Authorization: Bearer {token}"
```

---

#### GET `/api/shorteners/{id}`
Ruft Details eines einzelnen Shortlinks ab.

**Response:** Shortlink-Objekt (JSON)

**Beispiel:**
```bash
curl -H "Authorization: Bearer {token}" \
  http://localhost:8000/api/shorteners/1
```

---

### OAuth Endpoints

#### GET `/oauth/login`
Leitet zur Blikk-Authentifizierung um.

#### GET `/oauth/callback`
OAuth Callback-Endpoint (wird automatisch von Blikk aufgerufen).

#### POST `/oauth/logout`
Meldet den aktuellen Benutzer ab.

---

### Weitere Endpoints

#### POST `/language`
Wechselt die Sprache der Session.

**Body:**
```json
{
  "locale": "en" // oder "de", etc.
}
```

**Response:**
```json
{
  "message": "Locale updated successfully"
}
```

## 🛠️ Entwicklung

### Code-Stil

Das Projekt verwendet **Laravel Pint** als Code-Formatter.

#### Pint ausführen
```bash
# Aktuelle Änderungen formatieren
vendor/bin/pint --dirty --format agent

# Alle Dateien formatieren
vendor/bin/pint

# Prüfen ohne Änderungen
vendor/bin/pint --test
```

### Datenbankmodifikationen

#### Neue Migration erstellen
```bash
php artisan make:migration create_table_name
```

#### Migrationen ausführen
```bash
php artisan migrate
```

#### Rollback
```bash
# Letzter Batch
php artisan migrate:rollback

# Komplett zurücksetzen
php artisan migrate:reset

# Refresh mit Seeding
php artisan migrate:refresh --seed
```

### Modelle erstellen

```bash
# Model mit Factory und Migration
php artisan make:model ModelName -mf

# Mit Controller
php artisan make:model ModelName -mcr
```

### Artisan-Befehle

```bash
# Alle verfügbaren Befehle
php artisan list

# Hilfe für einen Befehl
php artisan {command} --help

# Datenbank-Konfiguration prüfen
php artisan config:show database.default

# Cache löschen
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Datenbank-Schema anzeigen
php artisan db:show
```

### Tinker REPL

Interaktive PHP-Konsole mit Laravel-Kontext:

```bash
php artisan tinker

# Oder direkter Befehl
php artisan tinker --execute 'User::count();'
```

### IDE-Setup

#### VS Code

Die Datei `.vscode/settings.json` enthält Einstellungen für:
- PHP Intelephense
- Laravel Artisan
- Tailwind CSS IntelliSense
- Vite Helper

#### Empfohlene Extensions

- Laravel Extension Pack
- Laravel Artisan
- Tailwind CSS IntelliSense
- Vite Helper
- PHP Intelephense
- Pest (PHP Testing)

## 🧪 Testen

Das Projekt verwendet **Pest** als Testing-Framework.

### Tests ausführen

```bash
# Alle Tests
php artisan test

# Mit kompaktem Output
php artisan test --compact

# Spezifische Test-Datei
php artisan test tests/Feature/ShortenerTest.php

# Spezifischen Test filtern
php artisan test --filter=test_create_shortener

# Mit Coverage
php artisan test --coverage

# Nur fehlgeschlagene Tests
php artisan test --failed
```

### Test-Struktur

```
tests/
├── Feature/
│   ├── ShortenerTest.php          # Feature Tests für Shortener
│   └── UserTest.php               # Feature Tests für User
├── Unit/
│   ├── Models/
│   │   ├── ShortenerTest.php      # Unit Tests für Shortener Model
│   │   └── UserTest.php           # Unit Tests für User Model
│   └── Services/
│       ├── QrCodeGeneratorTest.php
│       └── YouTubeConverterTest.php
└── Pest.php                       # Pest Konfiguration
```

### Neue Tests erstellen

```bash
# Feature Test
php artisan make:test ShortenerTest

# Unit Test
php artisan make:test ShortenerTest --unit

# Mit Pest
php artisan make:test ShortenerTest --pest
```

### Test-Beispiel (Pest Syntax)

```php
<?php

test('user can create shortener', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->post('/api/shorteners', [
            'url' => 'https://example.com/long/url',
            'description' => 'Test',
        ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('shortcuts', [
        'Url' => 'https://example.com/long/url',
    ]);
});
```

## 🚀 Deployment

### Vorbereitung

1. **Environment-Variablen**
   ```bash
   cp .env.example .env
   # Passen Sie die Produktions-Einstellungen an
   ```

2. **Application Key**
   ```bash
   php artisan key:generate
   ```

3. **Dependencies installieren**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install --omit=dev
   ```

4. **Frontend Assets kompilieren**
   ```bash
   npm run build
   ```

5. **Migrationen ausführen**
   ```bash
   php artisan migrate --force
   ```

6. **Cache vorbereiten**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Hosting-Optionen

#### Laravel Cloud (Empfohlen)
```bash
# Installation
composer require laravel/cloud

# Deployment via Cloud CLI
php artisan cloud:deploy
```

Siehe auch: [Laravel Cloud Dokumentation](https://cloud.laravel.com/)

#### Shared Hosting

1. Datei-Upload via FTP/SFTP
2. SSH-Zugriff für Artisan-Befehle
3. Datenbank-Setup (MySQL/PostgreSQL)
4. `.env` Datei mit Produktions-Werten

#### VPS/Dedicated Server

1. PHP 8.3+ Installation
2. Composer Installation
3. Node.js Installation
4. Git Setup für Deployment
5. Reverse Proxy (Nginx/Apache)
6. SSL-Zertifikat (Let's Encrypt)

**Beispiel Nginx-Konfiguration:**
```nginx
server {
    listen 80;
    server_name shortener.example.com;

    root /var/www/shortener/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Umgebungs-Variablen (Produktion)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://shortener.example.com

# Sichere DB-Verbindung
DB_CONNECTION=mysql
DB_HOST=db.example.com
DB_DATABASE=shortener_prod
DB_USERNAME=shortener_user
DB_PASSWORD=secure_password_here

# Redis für Cache/Sessions
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=redis.example.com
REDIS_PASSWORD=redis_password

# Mail-Konfiguration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password

# OAuth-Secrets
BLIKK_CLIENT_ID=prod_client_id
BLIKK_CLIENT_SECRET=prod_client_secret
```

## 🐛 Troubleshooting

### "Unable to locate file in Vite manifest"
Frontend Assets nicht kompiliert:
```bash
npm run build
# Oder im Development Mode:
npm run dev
```

### Database Connection Error
Überprüfen Sie die Datenbank-Einstellungen in `.env`:
```bash
php artisan migrate --verbose
```

### Permission Denied
Setzen Sie korrekte Verzeichnisrechte:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data /var/www/shortener
```

### composer.lock Issues
```bash
# Lock-Datei neu erstellen
rm composer.lock
composer install
```

### OAuth/Login Issues
1. Überprüfen Sie BLIKK_* Umgebungsvariablen
2. Validieren Sie Redirect URI in Blikk-Dashboard
3. Prüfen Sie Browser-Cookies und LocalStorage

## 📚 Weitere Ressourcen

- [Laravel Dokumentation](https://laravel.com/docs)
- [Laravel Socialite](https://laravel.com/docs/socialite)
- [Pest PHP](https://pestphp.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Vue.js](https://vuejs.org)
- [Vite](https://vitejs.dev)

## 📄 Lizenz

Dieses Projekt ist unter der MIT-Lizenz lizenziert. Siehe [LICENSE](LICENSE) Datei für Details.

## 👤 Autor

**Entwickelt von:** onkihara

**Kontakt:** Siehe GitHub-Profil

---

## 🤝 Beitragen

Beiträge sind willkommen! Bitte erstellen Sie einen Fork und einen Pull Request mit Ihren Verbesserungen.

### Development-Workflow

1. Fork das Repository
2. Feature-Branch erstellen: `git checkout -b feature/AmazingFeature`
3. Änderungen committen: `git commit -m 'Add AmazingFeature'`
4. Branch pushen: `git push origin feature/AmazingFeature`
5. Pull Request öffnen

### Anforderungen für PR

- Code mit Pint formatieren: `vendor/bin/pint --dirty`
- Tests schreiben/aktualisieren: `php artisan test`
- Dokumentation aktualisieren
- Aussagekräftige Commit-Messages

---

**Zuletzt aktualisiert:** 2024

Viel Spaß mit dem URL Shortener! 🚀
