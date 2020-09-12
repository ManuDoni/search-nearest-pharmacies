FROM phpearth/php:7.4-nginx

COPY . /var/www
COPY ./public /var/www/html

RUN apk add --no-cache composer

WORKDIR /var/www

RUN composer install

RUN chmod -R 777 storage/