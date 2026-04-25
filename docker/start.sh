#!/bin/sh
set -e

# Create SQLite database if not exists (volume mounted at /var/www/html/database)
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

# Ensure storage dirs exist
mkdir -p /var/www/html/storage/framework/{cache/data,sessions,views}
mkdir -p /var/www/html/storage/logs

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Run migrations
cd /var/www/html
php artisan migrate --force --no-interaction
php artisan db:seed --class=TemplateSeeder --force 2>/dev/null || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start services
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
