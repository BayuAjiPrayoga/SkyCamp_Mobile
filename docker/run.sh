#!/bin/sh
set -e

# Update Nginx port to match Railway provided PORT
# Replaces 'listen 8080' with 'listen $PORT'
echo "Configuring Nginx to listen on port ${PORT:-80}..."
sed -i "s/listen 8080;/listen ${PORT:-80};/g" /etc/nginx/sites-available/default

# Run Laravel optimizations
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Supervisor (which starts Nginx + PHP-FPM)
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
