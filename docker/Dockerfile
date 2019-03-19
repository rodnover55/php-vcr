FROM php

RUN apt update && \
    apt install -y git libxml2-dev && \
    docker-php-ext-install soap && \
    curl https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer && \
    composer global require hirak/prestissimo && \
    pecl install xdebug && \
    apt clean

COPY xdebug.ini /usr/local/etc/php/conf.d/15-xdebug.ini

RUN touch /var/log/xdebug.log && chown www-data:www-data /var/log/xdebug.log