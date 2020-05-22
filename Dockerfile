FROM php:7.4.6-apache

LABEL maintainer="kundik.kirill@gmail.com"
LABEL version="0.0.1"
LABEL description="supermetrics test task"

RUN a2enmod rewrite

RUN docker-php-ext-install pdo pdo_mysql
