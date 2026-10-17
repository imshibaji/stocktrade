# Docker

Run **StockTrade** (CodeIgniter 4 + SQLite + Tailwind CSS) in a container without
installing PHP, Composer or Node on the host.

## What's included

| File | Purpose |
|------|---------|
| `Dockerfile` | Multi-stage build: Tailwind assets → PHP base → Composer deps → runtime image |
| `.dockerignore` | Keeps the build context small and secrets out of the image |
| `docker-compose.yml` | One-service stack (`app`) with a persistent volume |
| `docker-compose.dev.yml` | Override for local development with live source mounts |
| `docker/apache/vhost.conf` | Apache virtual host pointing at `public/` |
| `docker/php.ini` | Production PHP/OPcache settings |
| `docker/entrypoint.sh` | Prepares `writable/`, runs migrations/seeds, starts Apache |
| `docker/env.example` | Documented list of supported environment variables |

The image is built on `php:8.2-apache` (override with `--build-arg PHP_VERSION=8.3`)
and Apache serves directly from `public/` — CodeIgniter's front controller.

## Quick start

```bash
# Build and start
docker compose up --build -d

# Open the app
open http://localhost:8080

# Follow logs
docker compose logs -f app

# Stop (keeps data)  /  stop and wipe data
docker compose down
docker compose down -v
```

Without Compose:

```bash
docker build -t stocktrade .
docker run --rm -p 8080:80 --name stocktrade stocktrade
```

## Configuration

All settings are plain environment variables (no `.env` file is baked into the
image). See `docker/env.example` for the full list. The most useful ones:

| Variable | Default | Description |
|----------|---------|-------------|
| `CI_ENVIRONMENT` | `production` | `production`, `development` or `testing` |
| `app_baseURL` | `http://localhost:8080/` | Base URL used to build links (keep the trailing slash) |
| `app_forceGlobalSecureRequests` | `false` | Force HTTPS URLs |
| `AUTO_MIGRATE` | `true` | Run `php spark migrate --all` on container start |
| `AUTO_SEED` | `false` | Run the seeder on start (use once on an empty database) |
| `SEEDER_CLASS` | `StockSeeder` | Seeder class used when `AUTO_SEED=true` |

`app_baseURL` must match the host/port you publish. For example, to run on port
`9000`, publish `9000:80` and set `app_baseURL=http://localhost:9000/`.

> **Use the underscore form for config keys** (`app_baseURL`, not `app.baseURL`).
> Apache's mod_php drops environment variable names that contain a dot, so the
> dotted form is silently ignored inside the container. CodeIgniter accepts
> both spellings; only the underscore form survives here.

### Seeding sample data

The SQLite database starts empty; migrations create the schema. To load the
bundled sample stocks (81 stocks plus generated price history and predictions):

```bash
# One-shot: runs migrations, then the seeder, then exits
docker compose run --rm app php spark db:seed StockSeeder

# Or against an already-running container
docker compose exec app php spark db:seed StockSeeder
```

To seed automatically on every boot (useful for a demo image), set
`AUTO_SEED: "true"` in `docker-compose.yml`. Only do this on an empty database —
`stocks.symbol` is unique, so re-seeding a populated database will fail.

## Data persistence

The container writes only to `/var/www/html/writable` (cache, logs, sessions,
uploads, debugbar and the SQLite database at `writable/database/stocktrade.db`).
Compose mounts the named volume `stocktrade-writable` there, so data survives
`docker compose down` and image rebuilds. `docker compose down -v` deletes it.

To use an existing SQLite file from the host:

```bash
docker run --rm -p 8080:80 \
  -v "$PWD/writable:/var/www/html/writable" \
  stocktrade
```

## Development (live editing)

```bash
docker compose -f docker-compose.yml -f docker-compose.dev.yml up --build
```

This bind-mounts the source tree and sets `CI_ENVIRONMENT=development`.
`vendor/` and `writable/` remain volumes inside the container.

Tailwind CSS is **not** recompiled inside the container — run the watcher on the
host alongside it:

```bash
npm install
npm run dev      # or: npm run build:css  for a one-off build
```

## Useful commands

```bash
# Shell inside the running container
docker compose exec app bash

# Run any CodeIgniter command
docker compose exec app php spark routes
docker compose exec app php spark migrate:status

# Run the test suite (dev deps are not in the image; install them first)
docker compose exec app composer install
docker compose exec app php vendor/bin/phpunit

# Rebuild from scratch
docker compose build --no-cache
```

## How the image is built

1. **assets** (`node:22-alpine`) — runs `npm ci` and `npm run build:css`, producing
   `public/css/style.css`.
2. **base** (`php:8.2-apache`) — installs `intl`, `zip`, `opcache` (the image
   already ships `json`, `mbstring`, `curl`, `sqlite3` and `pdo_sqlite`), enables
   `mod_rewrite`, points Apache at `public/`, and adds the Composer binary.
3. **vendor** — `composer install --no-dev --optimize-autoloader`.
4. **runtime** — copies the application, the `vendor/` directory and the compiled
   CSS, then prepares the `writable/` tree with `www-data` ownership.

Node never reaches the final image. The Composer binary is kept so you can
install dev dependencies (and run PHPUnit) inside a running container.

## Troubleshooting

- **Blank page / 500 on first request** — check `docker compose logs -f app`.
  The most common cause is a permission issue on `writable/`; the entrypoint
  fixes ownership on a named volume, but a bind-mounted directory from the host
  may need `chmod -R 775 writable` on the host.
- **Links point to the wrong host/port** — set `app_baseURL` to the URL you open
  in the browser (docker-compose.yml already sets `http://localhost:8080/`).
  Use the underscore form — dotted names are dropped by Apache's mod_php.
- **`Address already in use`** — another process (often a local `php spark serve`)
  is using port 8080; change the published port and `app_baseURL` together.
- **CSS looks unstyled** — the stylesheet is compiled at image build time. Rebuild
  the image (`docker compose build`) after changing `src/tailwind.css`.
