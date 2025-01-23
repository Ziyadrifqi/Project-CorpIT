# Menggunakan image PHP dengan Apache
FROM php:8.2-apache

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    iputils-ping \
    default-mysql-client \  
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd intl

# Enable mod_rewrite untuk Apache
RUN a2enmod rewrite

# Menentukan working directory
WORKDIR /var/www/html

# Meng-copy seluruh file aplikasi CodeIgniter ke container
COPY . /var/www/html
RUN chmod -R 755 /var/www/html

RUN chmod -R 775 /var/www/html/writable

# Mengatur Apache document root ke folder public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Mengatur izin untuk direktori proyek
RUN chown -R www-data:www-data /var/www/html

# Ekspos port 80 untuk container
EXPOSE 80

# Menjalankan Apache
CMD ["apache2-foreground"]