#!/bin/sh
# Container start-up: prepare storage, optionally migrate, warm the caches, then run the CMD.
set -e

cd /var/www/html

# A named volume mounted on /storage starts empty and hides the image's folders.
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
         storage/logs storage/app/public bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# artisan runs as www-data, never root: anything it writes (cache files, laravel.log) must stay
# writable by the web server and the queue worker that share the same volume.
artisan() { runuser -u www-data -- php artisan "$@"; }

# Only the web container should migrate (RUN_MIGRATIONS=true); workers must not race it.
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    artisan migrate --force
fi

# public/storage -> storage/app/public (idempotent)
artisan storage:link --force >/dev/null 2>&1 || true

# Caches are built from the real environment, so they must be created at start, not at build time.
artisan config:cache
artisan route:cache
artisan view:cache
artisan event:cache

# Apache starts as root and drops privileges itself; every other command (queue:work,
# schedule:work) runs as www-data.
if [ "$1" = "apache2-foreground" ]; then
    exec "$@"
fi

exec runuser -u www-data -- "$@"
