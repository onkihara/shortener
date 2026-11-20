# 🚀 Shortener-App - Quick Start Guide

## Datenbank-Setup

```bash
# Migrationen ausführen
php artisan migrate

# (Optional) Seed mit Test-Daten
php artisan tinker
# In Tinker:
Shortener::factory(10)->create();
Shortener::factory(5)->for(User::first())->create();
```

## Tests ausführen

```bash
# Alle Tests
php artisan test

# Nur Unit Tests
php artisan test tests/Unit

# Nur Feature Tests  
php artisan test tests/Feature

# Mit Coverage
php artisan test --coverage

# Spezifischer Test
php artisan test tests/Feature/ShortenerTest.php --filter=creates_shortener
```

## Development starten

```bash
# Backend starten (Laravel)
php artisan serve

# Frontend Assets (optional, Vite)
npm run dev

# Oder Production Build
npm run build
```

## URLs testen

### 1. Öffentliche Eingabemaske
```
http://localhost:8000/
```

### 2. Shortener erstellen (ohne Auth)
```bash
curl -X POST http://localhost:8000/api/shorten \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://example.com/very/long/url",
    "hash": "my-link",
    "description": "My description"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Shortener erfolgreich erstellt.",
  "data": {
    "id": 1,
    "hash": "my-link",
    "url": "https://example.com/very/long/url",
    "description": "My description",
    "short_link": "http://localhost:8000/my-link"
  }
}
```

### 3. Redirect testen
```
http://localhost:8000/my-link
# → Redirects to https://example.com/very/long/url
```

### 4. Dashboard (mit Auth)
```
http://localhost:8000/dashboard
# (Requires login first)
```

### 5. API: Shortener-Liste (mit Auth)
```bash
curl -H "Accept: application/json" \
  http://localhost:8000/api/shorteners
# (Requires authentication)
```

## Benutzer für Testing erstellen

```bash
php artisan tinker
# In Tinker:
User::create([
  'name' => 'Test User',
  'email' => 'test@example.com',
  'password' => bcrypt('password')
]);
```

## Häufige Probleme

### Problem: 404 auf /api/shorteners
- **Lösung**: Stellen Sie sicher, dass Sie angemeldet sind (`auth()->check()`)
- Routes sind auf `auth` Middleware konfiguriert

### Problem: CSRF Token Error
- **Lösung**: Frontend sendet automatisch Token via `X-CSRF-TOKEN` Header
- Oder Blade Form mit `@csrf`

### Problem: Hash bereits vergeben
- **Lösung**: Verwenden Sie einen anderen custom Hash oder lassen Sie einen generieren
- API generiert automatisch eindeutige Hashes

### Problem: Tests fehlgeschlagen
- **Lösung**: Datenbank muss aktuell sein
  ```bash
  php artisan migrate:fresh
  php artisan test
  ```

## Deployment-Checkliste

- [ ] `.env` mit korrekter DB konfigurieren
- [ ] `php artisan migrate` ausführen
- [ ] `php artisan config:cache` ausführen
- [ ] `npm run build` ausführen (für Production Assets)
- [ ] Dateirechte korrekt setzen (`storage/`, `bootstrap/cache/`)
- [ ] `APP_DEBUG=false` in Production
- [ ] `SESSION_SECURE_COOKIES=true` wenn HTTPS
- [ ] Rate-Limiting anpassen in `config/shortener.php`

## Weitere Features (Optional)

### Admin-Dashboard für Statistiken
```
GET /admin/dashboard
- Gesamtanzahl Shortener
- Top 10 meist aufgerufene Links
- Benutzer-Statistiken
```

### Link-Expiration
```php
// In StoreShortenerRequest
'expires_at' => 'nullable|date|after:now'

// In Shortener Model
public function scopeNotExpired($query)
{
    return $query->where('expires_at', '>', now())->orWhereNull('expires_at');
}
```

### QR-Code-Generation
```bash
composer require simplesoftwareio/simple-qrcode
```

```blade
<img src="{{ QrCode::size(300)->generate(url($shortener->Hash)) }}" />
```

### Analytics/Click-Tracking
```php
// Log each click with IP, User-Agent, Referrer
Schema::table('shortcuts', function (Blueprint $table) {
    $table->dateTime('last_accessed_at')->nullable();
});

// Or create separate clicks table
Schema::create('shortener_clicks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('shortener_id');
    $table->string('ip_address');
    $table->string('user_agent')->nullable();
    $table->string('referrer')->nullable();
    $table->timestamps();
});
```

---

**Happy Shortening! 🔗**
