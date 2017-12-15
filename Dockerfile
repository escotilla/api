FROM php:7.1-fpm

RUN apt-get update && apt-get install -y \
    zip unzip git libmcrypt-dev libcurl4-openssl-dev pkg-config libssl-dev \
        && docker-php-ext-install mbstring \
        && pecl install mongodb \
        && docker-php-ext-enable mongodb \
        && echo extension=mongodb.so >> /usr/local/etc/php/conf.d/mongodb.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD images/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

RUN usermod -u 1000 www-data

COPY . /var/www/html
RUN composer install --no-interaction

WORKDIR /var/www/html
RUN chown -R www-data /var/www/html

RUN chmod -R 0777 /var/www/html/storage/

CMD ["php-fpm"]
EXPOSE 9000