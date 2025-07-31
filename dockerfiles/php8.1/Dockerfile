FROM php:8.1-apache

# Cài đặt các package cần thiết
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Cấu hình và cài đặt PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mbstring \
    intl \
    mysqli \
    pdo_mysql \
    xml \
    zip \
    opcache

# Cài đặt Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Bật mod_rewrite cho Apache
RUN a2enmod rewrite

# Tạo thư mục cho ứng dụng
RUN mkdir -p /var/www/html

# Cấu hình Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Cấu hình PHP
COPY php.ini /usr/local/etc/php/php.ini

# Thiết lập quyền
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"] 