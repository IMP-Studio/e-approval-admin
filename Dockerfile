FROM php:8.1-alpine AS deps

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install

FROM php:8.1-alpine

RUN apk update && \
    apk add --no-cache \
        libzip \
        libpng \
        zlib \
        libwebp \
        nano \
        curl \
        build-base \
        libssl1.1 \
        zlib \
        libpng \
        libjpeg-turbo \
        freetype \
        libzip \
        icu-dev \
    && apk del build-base \
    && rm -rf /var/cache/apk/*

# Copy only necessary files from the composer stage
COPY --from=composer /app/vendor /var/www/html/app/vendor
COPY . /var/www/html/app

# Install PHP extensions
RUN docker-php-ext-install bcmath mysqli gettext gd zip pdo pdo_mysql && \
    pecl install redis && \
    docker-php-ext-enable redis && \
    docker-php-ext-configure gd --with-webp

# Set working directory
WORKDIR /var/www/html/app

# Optimize autoloader
RUN composer dump-autoload --optimize --classmap-authoritative

# Cleanup unnecessary files
RUN rm -rf /var/www/html/app/vendor/composer /var/www/html/app/composer.json /var/www/html/app/composer.lock

# Set permissions
RUN chown -R www-data:www-data /var/www/html/app

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy Apache vhost configuration
COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Copy custom php.ini configuration
COPY ./docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Cleanup unnecessary packages
RUN apk del libzip-dev libpng-dev zlib1g-dev libwebp-dev libicu-dev

# Remove cached files
RUN rm -rf /var/cache/apk/*

# Expose port
EXPOSE 80

# Entrypoint
CMD ["apache2-foreground"]
