#!/bin/sh
set -e

# Update Nginx port to match Railway provided PORT
echo "Configuring Nginx to listen on port ${PORT:-80}..."
sed -i "s/listen 8080;/listen ${PORT:-80};/g" /etc/nginx/sites-available/default

# Ensure storage permissions are correct at runtime
# This is critical for Laravel 500 errors
echo "Fixing storage permissions..."
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run Laravel optimizations
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (Force is required for production)
echo "Running migrations..."
php artisan migrate --force || echo "WARNING: Migration failed! Check logs."

# Start Supervisor (which starts Nginx + PHP-FPM)
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
