FROM php:7.2-apache

RUN apt-get update \
    && apt-get install -y wget

RUN docker-php-ext-install \
    pdo_mysql \
    && a2enmod \
    rewrite