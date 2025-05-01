FROM php:8.2-cli

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Laravel permissions
RUN chmod -R 777 storage bootstrap/cache

# Expose port Laravel will run on
EXPOSE 9000

# Run Laravel migrations and server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=9000

