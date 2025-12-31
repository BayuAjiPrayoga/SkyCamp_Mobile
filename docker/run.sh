#!/bin/sh

# Fail on any error
set -e

# Update Apache port to match Railway provided PORT
sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT:-80}>/g" /etc/apache2/sites-available/*.conf

# Run Laravel optimizations
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (Optional: safer to do manually but good for auto-deploy)
# echo "Running migrations..."
# php artisan migrate --force

# Start Apache
echo "Starting Apache..."
exec apache2-foreground
