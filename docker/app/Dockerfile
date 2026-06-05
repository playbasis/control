FROM php:5-fpm
MAINTAINER Burachit Tongthai <burachit.t@playbasis.com> (@rookawa)

# The official PHP image already includes PECL/PHPIZE tooling and the
# json/curl extensions. Debian php* packages are pinned out in this image.
RUN pecl install mongo-1.6.16 \
    && echo 'extension=mongo.so' > /usr/local/etc/php/conf.d/mongo.ini

COPY config/php.ini /usr/local/etc/php/

RUN rm -rf /var/www/control
VOLUME /var/www/control/

WORKDIR /var/www/control/

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 9000
CMD ["php-fpm"]
