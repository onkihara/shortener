# Expiration and Publish-Date Feature Implementation

## Overview
Diese Implementierung fügt Unterstützung für `PublishDate` und `ExpirationDate` Felder zu den Create- und Edit-Operationen des Shorteners hinzu. Sie ermöglicht es, Links zeitlich zu begrenzen.

## Implementierte Funktionen

### 1. Model (`app/Models/Shortener.php`)

#### Fillable Fields
```php
protected $fillable = [..., 'PublishDate', 'ExpirationDate'];
```

#### Date Casting
```php
protected function casts(): array
{
    return [
        ...,
        'ExpirationDate' => 'datetime',
    ];
}
```

#### Neue Methoden
- `isExpired(): bool` - Prüft ob der Link abgelaufen ist
- `scopePublished($query)` - Filtert nur veröffentlichte und nicht abgelaufene Links

### 2. Validation (`app/Http/Requests/`)

#### StoreShortenerRequest
```php
'publish_date' => ['nullable', 'date_format:Y-m-d H:i', 'after_or_equal:now'],
'expiration_date' => ['nullable', 'date_format:Y-m-d H:i', 'after:publish_date'],
```

#### UpdateShortenerRequest
Gleiche Validierung wie StoreShorteenerRequest für `publish_date` und `expiration_date`.

### 3. Controller (`app/Http/Controllers/ShortenerController.php`)

#### redirect() - Redirect Endpoint
```php
public function redirect(string $hash): RedirectResponse
{
    $shortener = Shortener::where('Hash', $hash)
        ->published()
        ->firstOrFail();

    if ($shortener->isExpired()) {
        abort(410);  // Gone
    }

    $shortener->recordAccess();
    return redirect($shortener->Url, 301);
}
```

#### store() - Create Shortener
- Akzeptiert `publish_date` und `expiration_date` als Request-Parameter
- Konvertiert Strings zu Carbon-DateTime-Objekten
- Speichert die Felder in der Datenbank

```php
'PublishDate' => $request->input('publish_date') 
    ? Carbon::createFromFormat('Y-m-d H:i', $request->input('publish_date')) 
    : null,
'ExpirationDate' => $request->input('expiration_date') 
    ? Carbon::createFromFormat('Y-m-d H:i', $request->input('expiration_date')) 
    : null,
```

#### update() - Edit Shortener
- Ermöglicht Aktualisierung von `publish_date` und `expiration_date`
- Nur Felder werden aktualisiert, die in der Request enthalten sind

#### show() - Get Single Shortener
- Gibt `publish_date` und `expiration_date` im Response zurück

#### getByHash() - Public API Endpoint
- Prüft auf abgelaufene Links
- Gibt 410 (Gone) zurück wenn Link abgelaufen ist

## API Endpoints

### Create Shortener
**POST** `/api/shorteners`

Request:
```json
{
  "url": "https://example.com",
  "hash": "mycustomhash",
  "description": "My Link",
  "publish_date": "2026-11-25 10:00",
  "expiration_date": "2026-12-25 10:00"
}
```

Response:
```json
{
  "success": true,
  "message": "Shortener erfolgreich erstellt.",
  "data": {
    "id": 1,
    "hash": "mycustomhash",
    "url": "https://example.com",
    "description": "My Link",
    "publish_date": "2026-11-25 10:00",
    "expiration_date": "2026-12-25 10:00",
    "short_link": "http://localhost/mycustomhash"
  }
}
```

### Edit Shortener
**PUT** `/api/shorteners/{id}`

Request:
```json
{
  "description": "Updated description",
  "publish_date": "2026-11-26 10:00",
  "expiration_date": "2026-12-26 10:00"
}
```

Response:
```json
{
  "success": true,
  "message": "Shortener erfolgreich aktualisiert.",
  "data": {
    "id": 1,
    "hash": "mycustomhash",
    "url": "https://example.com",
    "description": "Updated description",
    "frequency": 42,
    "publish_date": "2026-11-26 10:00",
    "expiration_date": "2026-12-26 10:00",
    "created_at": "2026-11-20T21:00:00+01:00",
    "updated_at": "2026-11-20T21:30:00+01:00"
  }
}
```

### Redirect
**GET** `/{hash}`

Response bei abgelaufenem Link:
- HTTP Status: `410 Gone`

Ansonsten:
- HTTP Status: `301 Moved Permanently`
- Redirect zur ursprünglichen URL

## Validierungsregeln

| Feld | Regel | Fehlermeldung |
|------|-------|---------------|
| publish_date | nullable, date_format: Y-m-d H:i, after_or_equal: now | Das Publish-Datum muss in der Zukunft oder jetzt sein. |
| expiration_date | nullable, date_format: Y-m-d H:i, after: publish_date | Das Verfallsdatum muss nach dem Publish-Datum liegen. |

## Datenbankschema

Die Tabelle `shortcuts` hat bereits diese Spalten:
- `PublishDate` (datetime, nullable)
- `ExpirationDate` (datetime, nullable)

Keine zusätzlichen Migrationen erforderlich.

## Tests

Testbeispiel in `tests/Feature/ExpirationAndPublishDateTest.php`:

```php
// Link wird nicht in published() Scope angezeigt wenn Publish-Datum in Zukunft ist
$shortener = Shortener::create([
    'Hash' => 'test789',
    'Url' => 'https://example.com',
    'PublishDate' => now()->addDay(),
    'ExpirationDate' => null,
]);

expect(Shortener::published()->where('ID', $shortener->ID)->exists())
    ->toBeFalse();

// Link wird nicht in published() Scope angezeigt wenn abgelaufen
$shortener = Shortener::create([
    'Hash' => 'test333',
    'Url' => 'https://example.com',
    'ExpirationDate' => now()->subDay(),
]);

expect(Shortener::published()->where('ID', $shortener->ID)->exists())
    ->toBeFalse();
```

## Verwendungsbeispiele

### Scenario 1: Link sofort veröffentlichen, in 30 Tagen ablaufen
```json
{
  "url": "https://example.com",
  "publish_date": null,
  "expiration_date": "2026-12-20 21:47:00"
}
```

### Scenario 2: Link in 1 Woche veröffentlichen, 2 Wochen später ablaufen
```json
{
  "url": "https://example.com",
  "publish_date": "2026-11-27 21:47:00",
  "expiration_date": "2026-12-04 21:47:00"
}
```

### Scenario 3: Link zeitlich unbegrenzt
```json
{
  "url": "https://example.com",
  "publish_date": null,
  "expiration_date": null
}
```

## Wichtige Hinweise

1. **Datumsformat**: Alle Daten verwenden das Format `YYYY-MM-DD HH:MM`
2. **Zeitzone**: Verwendet die in Laravel konfigurierte Zeitzone
3. **NULL-Werte**: 
   - `publish_date = null` → Link ist sofort veröffentlicht
   - `expiration_date = null` → Link läuft nie ab
4. **HTTP Status bei Ablauf**: 410 Gone wird zurückgegeben (nicht 404)
5. **Query Filtering**: `Shortener::published()` wird automatisch in Queries verwendet, um Links zu filtern
