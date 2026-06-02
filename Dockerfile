FROM php:8.1-apache

# 1. Instalasi library OS yang dibutuhkan
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# 2. Install ekstensi PHP (Termasuk dukungan MySQL dan PostgreSQL untuk Supabase)
RUN docker-php-ext-install intl mysqli pdo pdo_mysql pdo_pgsql pgsql zip

# 3. Aktifkan modul Rewrite Apache (Penting untuk routing CodeIgniter)
RUN a2enmod rewrite

# 4. Ubah DocumentRoot Apache ke folder 'public' milik CI4
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Tentukan lokasi kerja
WORKDIR /var/www/html

# 6. Salin semua file dari komputer Anda ke server container
COPY . .

# 7. Install Composer untuk mengunduh dependency CI4
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 8. Berikan hak akses penuh pada folder writable agar CI4 bisa menyimpan log & cache
RUN chmod -R 777 writable/

# Buka port 80 (Port default web)
EXPOSE 80
