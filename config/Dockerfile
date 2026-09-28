FROM php:8.4-apache

# System packages
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    nano \
    mc \
    vim \
    less \
    procps \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libavif-dev \
    libicu-dev \
    libxml2-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Configure GD
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    --with-avif

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    opcache

# Copy custom Apache virtual host configuration
COPY config/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache mod_rewrite if your app needs it (common for frameworks)
RUN a2enmod rewrite headers expires

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set the working directory
WORKDIR /var/www/html

# Optional: Drush
RUN composer global require drush/drush

RUN curl -L https://github.com/xdebug/xdebug/archive/refs/tags/3.5.3.tar.gz -o /tmp/xdebug.tar.gz \
    && pecl install /tmp/xdebug.tar.gz \
    && docker-php-ext-enable xdebug

COPY config/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ENV PATH="/root/.composer/vendor/bin:${PATH}"

EXPOSE 80
