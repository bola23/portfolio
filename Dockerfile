
# ==========================================
# Stage 1: Build frontend assets using Vite
# ==========================================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./

# Install frontend dependencies
RUN npm ci

COPY . .

# Compile CSS and JavaScript
RUN npm run build


# ==========================================
# Stage 2: Laravel production environment
# ==========================================
FROM php:8.3-fpm-bookworm

# Install required system packages
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    gettext-base \
    git \
    unzip \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        bcmath \
        intl \
        gd \
        zip \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set Laravel working directory
WORKDIR /var/www/html

# Copy Laravel source code
COPY . .

# Install production PHP dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Copy compiled Vite assets
COPY --from=frontend /app/public/build ./public/build

# Create required directories and permissions
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Copy Nginx configuration
COPY docker/nginx.conf.template /etc/nginx/templates/default.conf.template

# Copy Supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf

# Copy startup script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint

# Render will provide the PORT environment variable
EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/entrypoint"]

CMD ["supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
