FROM php:8.2-fpm

# Install system dependencies, extensions, dll (disesuaikan)
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev && \
    docker-php-ext-install zip pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ⛳️ PENTING: Copy seluruh isi project terlebih dahulu
COPY . .

# 🔧 Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader --verbose

# ✅ Set permission jika perlu
RUN chown -R www-data:www-data /var/www && chmod -R 775 /var/www/storage

RUN php artisan storage:link

EXPOSE 8080

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
