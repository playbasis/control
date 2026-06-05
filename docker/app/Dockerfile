FROM php:5-fpm
MAINTAINER Burachit Tongthai <burachit.t@playbasis.com> (@rookawa)

# The official PHP image already includes PECL/PHPIZE tooling and the
# json/curl extensions. Debian php* packages are pinned out in this image.
RUN set -eux; \
    sed -i \
        -e 's|deb.debian.org/debian|archive.debian.org/debian|g' \
        -e 's|security.debian.org/debian-security|archive.debian.org/debian-security|g' \
        -e '/stretch-updates/d' \
        /etc/apt/sources.list; \
    echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until; \
    apt-get update; \
    apt-get install -y --no-install-recommends --allow-unauthenticated libssl1.0-dev; \
    pecl install https://pecl.php.net/get/mongo-1.6.16.tgz; \
    echo 'extension=mongo.so' > /usr/local/etc/php/conf.d/mongo.ini; \
    apt-get purge -y --auto-remove libssl1.0-dev; \
    rm -rf /var/lib/apt/lists/* /tmp/pear ~/.pearrc

COPY config/php.ini /usr/local/etc/php/

RUN rm -rf /var/www/control
VOLUME /var/www/control/

WORKDIR /var/www/control/

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 9000
CMD ["php-fpm"]
