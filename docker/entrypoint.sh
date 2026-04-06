#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
  cp .env.docker.example .env
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

php artisan key:generate --force >/dev/null 2>&1 || true

exec "$@"
