FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required for Laravel and Excel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip \
    xml \
    mbstring \
    bcmath

# Enable Apache mod_rewrite for Laravel
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies with platform requirements check disabled
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

# Copy application files
COPY . .

# Complete composer installation
RUN composer dump-autoload --optimize --no-dev

# Set Apache document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Create storage and cache directories
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# NUCLEAR OPTION: Fix Apache MPM Conflict
# Remove ALL MPM configurations from mods-enabled to ensure clean state
# Then explicitly enable ONLY mpm_prefork
RUN rm -f /etc/apache2/mods-enabled/mpm_* \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Fix: Bind Apache to Railway's dynamic PORT
# Railway injects a PORT variable, Apache must listen on it
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Create entrypoint script to handle env substitution
RUN echo '#!/bin/bash\n sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf && sed -i "s/:80/:${PORT:-80}/g" /etc/apache2/sites-available/*.conf && exec apache2-foreground' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Use the new entrypoint
CMD ["/usr/local/bin/docker-entrypoint.sh"]
