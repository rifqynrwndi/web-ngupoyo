FROM php:8.2-fpm

WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    git \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project
COPY . /var/www

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Set permission
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

EXPOSE 8080

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
