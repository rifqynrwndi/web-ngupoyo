# Gunakan image PHP dengan extension yang dibutuhkan
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set workdir
WORKDIR /var/www

# Copy source
COPY composer.json composer.lock ./

# Step 2: Copy only config and artisan for package:discover
COPY artisan artisan
COPY config/ config/

# Step 3: Composer install
RUN composer install --no-dev --optimize-autoloader --verbose

# Step 4: Baru copy semua
COPY . .

# Set permission
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Expose port
EXPOSE 8080

# Start Laravel built-in server
CMD php artisan serve --host=0.0.0.0 --port=8080
