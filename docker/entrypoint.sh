#!/bin/sh
set -e

# Install dependencies if vendor folder is missing or if composer.json changed
if [ ! -d "vendor" ]; then
    echo "Vendor directory not found. Installing dependencies..."
    composer install --no-interaction --optimize-autoloader --no-dev
fi

# Run database migrations
echo "Running migrations..."
php artisan migrate --force

# Ensure storage link exists
if [ ! -d "public/storage" ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

# Clear and cache config/routes/views for performance
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data storage bootstrap/cache

echo "Application is ready!"

# Execute the main command (php-fpm)
exec "$@"
