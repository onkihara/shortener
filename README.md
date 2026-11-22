# URL Shortener

A Laravel application for creating and managing short URLs. It supports custom
aliases, scheduled publication and expiration, QR-code generation, click counts,
and optional OAuth sign-in through Laravel Socialite.

## Contents

- [Features](#features)
- [Technology and architecture](#technology-and-architecture)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Using the application](#using-the-application)
- [HTTP endpoints](#http-endpoints)
- [Development and testing](#development-and-testing)
- [Production deployment](#production-deployment)
- [Troubleshooting](#troubleshooting)

## Features

- Create short URLs anonymously or while signed in.
- Generate a unique short code automatically or request a custom alias.
- Set an optional publication date and expiration date.
- Add a description and track the number of recorded accesses.
- Generate QR codes in PNG, GIF, SVG, or WebP format, with configurable size
  and colors.
- Manage links in an authenticated dashboard and through authenticated JSON
  endpoints.
- Soft-delete links by default; an explicit option is available for permanent
  deletion.
- Redirect YouTube watch and short links to a privacy-enhanced embed URL.
- Localize the interface using the configured application locales.
- Sign in through an OAuth provider using Laravel Socialite.

## Technology and architecture

The versions below are the constraints declared by this repository, not a
guarantee that every dependency is installed at the highest listed version.

| Component | Version constraint | Purpose |
| --- | --- | --- |
| PHP | `^8.3` | Application runtime |
| Laravel | `^13.17` | Web framework |
| Laravel Socialite | `^5.33` | OAuth authentication |
| Vue | `^3.5` | Front-end JavaScript framework |
| Tailwind CSS | `^4.0` | CSS framework |
| Vite | `^8.0` | Front-end development and build tool |
| Pest | `^5.3` | Test framework |
| Endroid QR Code | `^6.2` | QR-code generation |

### Application structure

```text
app/
├── Http/
│   ├── Controllers/       # Web and JSON request handling
│   ├── Middleware/        # Request middleware, including locale selection
│   └── Requests/          # Input validation
├── Models/                # Eloquent models
├── Policies/              # Authorization policies
└── Services/              # QR-code and YouTube URL services
config/                    # Laravel and shortener configuration
database/
├── migrations/            # Database schema
├── factories/
└── seeders/
resources/
├── js/                    # Front-end JavaScript
├── css/
└── views/                 # Blade templates
routes/
└── web.php                # Web pages and JSON endpoints
tests/
├── Feature/
└── Unit/
```

The app's HTTP endpoints are currently defined in `routes/web.php`; the JSON
endpoints use Laravel's normal web middleware and session-based authentication.
They are not configured as stateless bearer-token API routes.

### Short-link data

Short links are stored in the `shortcuts` table. The `Shortener` model maps the
legacy column names to Laravel Eloquent and uses soft deletes.

| Column | Description |
| --- | --- |
| `ID` | Auto-incrementing primary key |
| `UserID` | Optional ID of the owner |
| `Hash` | Unique short code (up to 16 characters) |
| `Url` | Destination URL (up to 1,024 characters) |
| `Description` | Optional description (up to 1,024 characters) |
| `Frequency` | Recorded access count |
| `CreationDate`, `UpdateDate` | Creation and update timestamps |
| `PublishDate` | Optional time at which the link becomes available |
| `ExpirationDate` | Optional time after which the link expires |
| `DeleteDate` | Soft-delete timestamp |

Automatic hashes use configurable lengths of 4 to 8 characters by default.
Custom hashes are limited to 16 characters and the configured letters, digits,
hyphen, and underscore.

### Main services

- `QrCodeGenerator` generates base64-encoded QR-code images. Supported formats
  are PNG, GIF, SVG, and WebP.
- `YouTubeConverter` recognizes YouTube watch and `youtu.be` URLs and converts
  valid video IDs to `youtube-nocookie.com` embed URLs.

## Requirements

- PHP 8.3 or later and the extensions required by Laravel and the installed
  Composer packages.
- Composer 2.
- Node.js and npm versions supported by the Vite version in `package.json`.
- SQLite, MySQL, or PostgreSQL. SQLite is the default in `.env.example`.

For production, use a supported PHP-FPM web server setup and point the web
server's document root to the application's `public/` directory.

## Installation

### 1. Get the application

```bash
git clone https://github.com/onkihara/shortener.git
cd shortener
```

### 2. Install PHP dependencies and configure the environment

```bash
composer install
cp .env.example .env
php artisan key:generate
```

On Windows, copy `.env.example` to `.env` using Explorer or:

```powershell
Copy-Item .env.example .env
```

Edit `.env` and set at least `APP_NAME`, `APP_URL`, and your database
connection. Keep `APP_DEBUG=true` only for local development.

### 3. Configure the database

For SQLite, set `DB_CONNECTION=sqlite`. If the database file does not exist,
create it before running migrations:

```bash
touch database/database.sqlite
php artisan migrate
```

On Windows, create the file with:

```powershell
New-Item database/database.sqlite -ItemType File
php artisan migrate
```

For MySQL or PostgreSQL, create a database and user, then set the relevant
`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and
`DB_PASSWORD` values in `.env` before migrating:

```bash
php artisan migrate
```

### 4. Install and build front-end assets

```bash
npm install
npm run build
```

### 5. Start the local application

In one terminal, start Laravel:

```bash
php artisan serve
```

In another terminal, start Vite if you are working on front-end assets:

```bash
npm run dev
```

Open the URL printed by `php artisan serve` (typically
`http://127.0.0.1:8000`).

### Optional one-command setup

The Composer `setup` script installs PHP and JavaScript dependencies, creates
the environment file if needed, generates an application key, runs migrations,
and builds assets:

```bash
composer run setup
```

Review `.env` and configure the database before using this script. It runs
migrations as part of setup.

## Configuration

### Environment variables

The `.env.example` file contains the standard Laravel settings. Common values
include:

```dotenv
APP_NAME="URL Shortener"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

For a network database, configure the connection-specific variables in `.env`.
Do not commit `.env` or production credentials to source control.

### Shortener options

`config/shortener.php` controls hash generation, URL validation, descriptions,
and pagination. Defaults include:

- Hash lengths: 4, 5, 6, 7, and 8 characters, tried in order.
- Maximum hash length: 16 characters.
- Allowed URL schemes: HTTP, HTTPS, FTP, and FTPS.
- Maximum URL and description length: 1,024 characters each.
- Dashboard list page size: 15 links.

After changing cached configuration in a deployed environment, rebuild the
configuration cache:

```bash
php artisan config:cache
```

### OAuth sign-in with Laravel Socialite

Laravel Socialite provides a consistent OAuth flow across supported identity
providers. The application has `/oauth/login` and `/oauth/callback` routes for
starting authentication and processing the provider response. Socialite
credentials and redirect URIs should be supplied through environment variables
and registered in `config/services.php`; do not place secrets in source code.

A provider entry in `config/services.php` generally follows this pattern:

```php
'provider-name' => [
    'client_id' => env('OAUTH_CLIENT_ID'),
    'client_secret' => env('OAUTH_CLIENT_SECRET'),
    'redirect' => env('OAUTH_REDIRECT_URI'),
],
```

The login and callback code must use the same Socialite driver name as the
configuration entry. Set the OAuth application's callback URL to the
application's `/oauth/callback` URL. Provider availability and supported user
fields vary; a provider not included with Socialite may require a compatible
community provider package. Configure such a provider before enabling sign-in.

## Using the application

### Web interface

- `/` displays the landing page to signed-out visitors and sends signed-in
  users to the create-link page.
- `/shortener/create` displays the link creation page.
- `/shortener/dashboard` displays the signed-in user's dashboard.
- `/oauth/login` starts OAuth sign-in; `/oauth/logout` signs out.

The short-link creation endpoint is also available to guests. Guest-created
links are not associated with a user account and cannot be managed through the
owner-only update and delete operations.

### Redirect behavior

Visit `https://your-domain.example/{hash}` to follow a short link. A published,
unexpired link redirects to its destination and records an access. A link whose
publication date is in the future returns HTTP 409; an expired link returns
HTTP 410. Unknown and soft-deleted hashes return HTTP 404.

### Languages

The `POST /language` endpoint changes the locale stored in the current session.
The requested locale must be among the locales configured by the application.

## HTTP endpoints

The JSON endpoints return a `success` boolean and either a `data` payload or an
error message. Public endpoints are rate-limited to 30 requests per minute.
Authenticated management endpoints use the Laravel session created at sign-in;
they do not accept a bearer token by default.

| Method | Path | Access | Purpose |
| --- | --- | --- | --- |
| `POST` | `/api/shorten` | Public | Create a short link |
| `GET` | `/api/shorten?hash={hash}` | Public | Look up a published short link |
| `POST` | `/api/qr-code` | Public | Generate a QR code |
| `GET` | `/api/shorteners` | Signed in | List the current user's links |
| `GET` | `/api/shorteners/{shortener}` | Signed in | Get a link's details |
| `PATCH`, `PUT` | `/api/shorteners/{shortener}` | Owner | Update description or dates |
| `DELETE` | `/api/shorteners/{shortener}` | Owner | Soft-delete a link |

The redirect route is `GET /{hash}`. The OAuth and locale routes are described
above.

### Create a short link

`POST /api/shorten` accepts JSON or form input:

```json
{
  "url": "https://example.com/a/long/path",
  "hash": "my-link",
  "description": "Optional note",
  "publish_date": "2026-11-25 09:00",
  "expiration_date": "2026-12-01 09:00:00"
}
```

`url` is required and must use HTTP, HTTPS, FTP, or FTPS. `hash` is optional;
if omitted, a unique code is generated. Publication dates use the
`Y-m-d H:i` format; expiration dates use `Y-m-d H:i:s`. When both dates are
provided, expiration must not be earlier than publication. A successful
response has HTTP 201 and includes the short link in `data.short_link`.

Example:

```bash
curl -X POST http://127.0.0.1:8000/api/shorten \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{
    "url": "https://example.com/a/long/path",
    "description": "Example link"
  }'
```

### Look up a link

```bash
curl -G http://127.0.0.1:8000/api/shorten \
  --data-urlencode 'hash=abc123' \
  -H 'Accept: application/json'
```

The endpoint returns the destination URL and description for a published,
unexpired link. It also increments the access count.

### Manage links

The following authenticated endpoints use the browser session established by
OAuth sign-in. They are intended for the application's web client. The
`GET /api/shorteners` endpoint supports an optional `search` query and returns
paginated results.

Updates accept `description`, `publish_date`, and `expiration_date`. Deletion
is soft by default. To permanently delete a link, pass `hard=true` to the
delete request. Only the authenticated owner can update or delete a link.

### Generate a QR code

`POST /api/qr-code` accepts `text` (required), plus optional `format`,
`foreground_color`, `background_color`, and `size` fields. Formats are `png`,
`gif`, `svg`, and `webp`; colors must be six-digit hexadecimal values, and size
must be between 100 and 1,000 pixels. The response includes a base64 data URL.

Example:

```bash
curl -X POST http://127.0.0.1:8000/api/qr-code \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"text":"https://example.com/abc123","format":"png","size":300}'
```

## Development and testing

### Run the tests

```bash
php artisan test
```

The test suite uses Pest. Existing tests cover shortener behavior, date
handling, QR-code generation, localization, and YouTube URL conversion.

### Format PHP code

```bash
vendor/bin/pint --dirty --format agent
```

### Useful Laravel commands

```bash
php artisan route:list
php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Production deployment

The following is a general Laravel deployment outline. Adapt it to the hosting
platform and its PHP, database, and web-server requirements.

1. Provision a supported PHP runtime, required PHP extensions, a database, and
   a web server. Configure the web root to point to `public/`.
2. Set production environment variables securely. Use
   `APP_ENV=production`, `APP_DEBUG=false`, a correct `APP_URL`, and production
   database credentials.
3. Install PHP dependencies and build the front-end assets:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   ```

4. Generate `APP_KEY` once for the deployment environment if it has not
   already been generated. Do not generate a new key on each release when
   preserving encrypted application data.
5. Run migrations during deployment:

   ```bash
   php artisan migrate --force
   ```

6. Cache production configuration and views:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

7. Ensure `storage/` and `bootstrap/cache/` are writable by the PHP process,
   enable HTTPS, and verify that the application health and login flows work.

Do not copy the development `.env` file into a public directory or expose
credentials in deployment logs. Select session, cache, queue, and mail
backends appropriate for the hosting environment.

## Troubleshooting

### Vite manifest is missing

Build the front-end assets, or run the Vite development server:

```bash
npm run build
# For local development:
npm run dev
```

### Database connection or missing-table errors

Check the database settings in `.env`, ensure the database exists, and run:

```bash
php artisan migrate
```

### OAuth callback errors

Verify that the provider credentials are configured in `config/services.php`,
that the login and callback code use the configured Socialite driver, and that
the callback URL registered with the identity provider exactly matches
`APP_URL` plus `/oauth/callback`.

### Storage permission errors

Grant the PHP process write access to `storage/` and `bootstrap/cache/`. Avoid
making the entire project tree world-writable.

## License

This project is licensed under the [MIT License](LICENSE).

---

Last updated: November 2026
