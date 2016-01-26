FROM php:5-fpm
MAINTAINER Burachit Tongthai <burachit.t@playbasis.com> (@rookawa)

RUN apt-get update && apt-get install -y \
        php-pear \
        php5-dev \
        libcurl4-gnutls-dev \
    && docker-php-ext-install \
        json \
        curl

RUN pecl install mongo
RUN echo 'extension=mongo.so' | tee /usr/local/etc/php/conf.d/mongo.ini

COPY config/php.ini /usr/local/etc/php/

RUN rm -rf /var/www/control
VOLUME /var/www/control/

WORKDIR /var/www/control/

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 9000
CMD ["php-fpm"]
