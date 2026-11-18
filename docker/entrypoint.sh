
#!/bin/sh

set -e

echo "Starting Laravel application..."

# Use Render's assigned port
export PORT="${PORT:-10000}"

# Generate Nginx configuration
envsubst '${PORT}' \
    < /etc/nginx/templates/default.conf.template \
    > /etc/nginx/conf.d/default.conf

# Ensure Laravel directories exist
mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Set permissions
chown -R www-data:www-data storage bootstrap/cache

# Create public storage symlink
php artisan storage:link || true

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Cache Laravel configuration
echo "Caching Laravel configuration..."
php artisan config:cache

echo "Laravel application is ready!"

# Start Supervisor
exec "$@"
