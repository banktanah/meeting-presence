#!/bin/bash
# Clear all Laravel caches

echo "Clearing all caches..."

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# If using opcache, restart php-fpm
if command -v systemctl &> /dev/null; then
    echo "Restarting PHP-FPM..."
    sudo systemctl restart php*-fpm || sudo systemctl restart php-fpm
fi

echo "Cache cleared successfully!"
echo ""
echo "Current API_DASHBOARD_ENDPOINT:"
php artisan tinker --execute="echo config('app.api_endpoint.dashboard');"
