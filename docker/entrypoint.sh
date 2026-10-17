#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# Container entrypoint for the Tickmoss (CodeIgniter 4) application.
#
# 1. Prepares the writable/ tree
# 2. Optionally runs database migrations  (AUTO_MIGRATE, default: true)
# 3. Optionally seeds sample data        (AUTO_SEED,    default: false)
# 4. Fixes ownership of writable/ so Apache's www-data can write
# 5. Hands over to the official PHP image entrypoint (docker-php-entrypoint)
# ---------------------------------------------------------------------------
set -euo pipefail

APP_DIR="/var/www/html"
cd "$APP_DIR"

log() { printf '%s [entrypoint] %s\n' "$(date -u '+%Y-%m-%dT%H:%M:%SZ')" "$*"; }

# 1. writable/ is the only directory CodeIgniter writes to.
mkdir -p \
    writable/cache \
    writable/logs \
    writable/session \
    writable/uploads \
    writable/debugbar \
    writable/database

if [ ! -f vendor/autoload.php ]; then
    log "ERROR: vendor/autoload.php is missing — the image was not built correctly."
    exit 1
fi

# 2. Database migrations (the SQLite file lives in writable/database/).
if [ "${AUTO_MIGRATE:-true}" = "true" ]; then
    log "Running database migrations (AUTO_MIGRATE=true) ..."
    php spark migrate --all || log "WARNING: migrations reported an error — continuing."
fi

# 3. Optional seed data.
if [ "${AUTO_SEED:-false}" = "true" ]; then
    log "Seeding database with ${SEEDER_CLASS:-StockSeeder} (AUTO_SEED=true) ..."
    php spark db:seed "${SEEDER_CLASS:-StockSeeder}" || log "WARNING: seeder reported an error — continuing."
fi

# 4. Hand writable/ (including the SQLite files just created) to the web user.
if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data writable
    chmod -R u+rwX,g+rwX writable
fi

log "Starting: $*"
exec docker-php-entrypoint "$@"
