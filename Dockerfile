# Gunakan image resmi PHP 8.2 dengan FPM-Alpine (ringan)
FROM php:8.2-fpm-alpine

# Set direktori kerja di dalam kontainer
WORKDIR /var/www/html

# Install dependensi yang dibutuhkan oleh Laravel & PHP
RUN apk add --no-cache \
    build-base shadow \
    curl \
    libxml2-dev \
    oniguruma-dev \
    openssl-dev \
    mariadb-client \
    && docker-php-ext-install \
    pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer (manajer paket PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin semua file proyek Anda ke dalam kontainer
COPY . .

# Jalankan composer install untuk mengunduh package Laravel
RUN composer install --no-dev --optimize-autoloader

# Set izin folder agar bisa ditulis oleh server
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Buka port 8000 agar bisa diakses
EXPOSE 8000

# Perintah default untuk menjalankan server saat kontainer dimulai
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]