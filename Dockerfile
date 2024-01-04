FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    default-mysql-client libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && echo "extension=imagick.so" >> /usr/local/etc/php/conf.d/docker-php-ext-imagick.ini \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install pdo_mysql

