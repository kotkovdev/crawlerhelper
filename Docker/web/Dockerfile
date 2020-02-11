FROM php:7.2-apache

RUN apt-get update \
    && apt-get install -y wget

RUN docker-php-ext-install \
    pdo_mysql \
    && a2enmod \
    rewrite

RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_host=192.168.1.102" >> /usr/local/etc/php/conf.d/xdebug.ini