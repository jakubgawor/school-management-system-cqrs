FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y libzip-dev zip \
    && docker-php-ext-install zip pdo_mysql \
    && pecl install xdebug redis \
    && docker-php-ext-enable xdebug redis \
    && a2enmod rewrite \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN service apache2 restart

WORKDIR /var/www