FROM php:8.1-apache-buster

RUN apt-get update && apt-get install -y libzip-dev libpng-dev zlib1g-dev libwebp-dev

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

RUN apt-get install -y nano curl build-essential libssl-dev zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libicu-dev && apt-get update && apt-get clean

# install php dependencies
RUN pecl install redis
RUN docker-php-ext-install bcmath mysqli gettext gd zip pdo pdo_mysql
RUN docker-php-ext-enable redis
RUN docker-php-ext-configure gd --with-webp

WORKDIR /var/www/html/app

COPY . /var/www/html/app

RUN composer install

RUN a2enmod rewrite

# set vhost
COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN chown -R www-data:www-data /var/www/html/app

# set max upload
COPY ./docker/php.ini /usr/local/etc/php

ADD start.sh /start.sh
RUN chmod 777 /start.sh

ENTRYPOINT ["/start.sh"]
