# Dockerfile pour Render.com - TheCrossLove Symfony 7.3

# Stage 1: Build
FROM php:8.2-apache AS builder

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies (no dev for production)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy application files
COPY . .

# Run composer scripts after copying all files
RUN composer run-script post-install-cmd --no-interaction || true

# Stage 2: Production
FROM php:8.2-apache

# Install production dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring gd zip intl opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configure Apache virtual host for Symfony
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        FallbackResource /index.php\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# PHP production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configure PHP for production
RUN echo "memory_limit=256M" >> "$PHP_INI_DIR/conf.d/symfony.ini" \
    && echo "upload_max_filesize=10M" >> "$PHP_INI_DIR/conf.d/symfony.ini" \
    && echo "post_max_size=10M" >> "$PHP_INI_DIR/conf.d/symfony.ini" \
    && echo "opcache.enable=1" >> "$PHP_INI_DIR/conf.d/symfony.ini" \
    && echo "opcache.memory_consumption=256" >> "$PHP_INI_DIR/conf.d/symfony.ini" \
    && echo "opcache.max_accelerated_files=20000" >> "$PHP_INI_DIR/conf.d/symfony.ini" \
    && echo "realpath_cache_size=4096K" >> "$PHP_INI_DIR/conf.d/symfony.ini" \
    && echo "realpath_cache_ttl=600" >> "$PHP_INI_DIR/conf.d/symfony.ini"

# Set working directory
WORKDIR /var/www/html

# Copy from builder
COPY --from=builder /var/www/html /var/www/html

# Create var directory with proper permissions
RUN mkdir -p var/cache var/log public/uploads/events \
    && chown -R www-data:www-data var public/uploads \
    && chmod -R 775 var public/uploads

# Set environment variables
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Expose port (Render uses PORT env variable)
EXPOSE 80

# Create entrypoint script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Use PORT from Render or default to 80\n\
if [ -n "$PORT" ]; then\n\
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf\n\
fi\n\
\n\
# Clear and warm up cache\n\
php bin/console cache:clear --env=prod --no-debug || true\n\
php bin/console cache:warmup --env=prod --no-debug || true\n\
\n\
# Run migrations if database is configured\n\
if [ -n "$DATABASE_URL" ]; then\n\
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true\n\
fi\n\
\n\
# Fix permissions\n\
chown -R www-data:www-data var public/uploads\n\
\n\
# Start Apache\n\
exec apache2-foreground\n\
' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
