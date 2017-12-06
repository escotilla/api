FROM php:fpm

RUN apt-get update && apt-get install -y \
    zip unzip git libmcrypt-dev libcurl4-openssl-dev pkg-config libssl-dev \
        && docker-php-ext-install mbstring \
        && pecl install mongodb \
        && docker-php-ext-enable mongodb \
        && echo extension=mongodb.so >> /usr/local/etc/php/conf.d/mongodb.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD images/nginx/nginx.conf /etc/nginx/conf.d/default.conf

COPY . /var/www/html
RUN composer install --no-interaction
RUN chown -R www-data:www-data /var/www/html
RUN chown -R :www-data /var/www/html
RUN chown www-data:www-data -R /var/www/html/storage/
RUN chmod -R 777 /var/www/html
RUN chmod -R 777 /var/www/html/storage