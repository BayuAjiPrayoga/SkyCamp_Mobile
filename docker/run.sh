#!/bin/sh

# Fail on any error
set -e

# NUCLEAR FIX: Remove conflicting MPMs at RUNTIME
# This ensures no other process re-enabled them during startup
rm -f /etc/apache2/mods-enabled/mpm_event.load
rm -f /etc/apache2/mods-enabled/mpm_event.conf
rm -f /etc/apache2/mods-enabled/mpm_worker.load
rm -f /etc/apache2/mods-enabled/mpm_worker.conf

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
