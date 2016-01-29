#!/bin/bash
set -e

if [ "$1" == php-fpm ]; then
    if [ -n "$MONGO_PORT_27017_TCP" ]; then
        if [ -z "$MONGO_HOSTBASE" ]; then
            export MONGO_HOSTBASE='mongo'
        else
            echo >&2 'warning: both MONGO_HOSTBASE and MONGO_PORT_27017_TCP found'
            echo >&2 "  Connecting to MONGO_HOSTBASE ($MONGO_HOSTBASE)"
            echo >&2 '  instead of the linked mysql container'
        fi
    fi

    if [ -z "$MONGO_HOSTBASE" ]; then
        echo >&2 'error: Mongo host is uninitialized and MONGO_HOSTBASE not set'
        echo >&2 '  Did you forget to add -e MONGO_HOSTBASE=... ?'
        exit 1
    fi

    cd /var/www/api/application/config;
    cp config-example.php config.php;
    #cp database-example.php database.php;
    cp mongodb-example.php mongodb.php;
    sed -i -e "/mongo_hostbase/s/= '.*:27017'/= \$_SERVER['MONGO_HOSTBASE']/" mongodb.php;
    sed -i -e "/mongo_username/s/= '[^\s]*'/= \$_SERVER['MONGO_USERNAME']/" mongodb.php;
    sed -i -e "/mongo_password/s/= '[^\s]*'/= \$_SERVER['MONGO_PASSWORD']/"  mongodb.php;
    # cat mongodb.php
    cp playbasis-example.php playbasis.php;
    sed -i.bak "/REPORT_EMAIL_CLIENT/s/true/false/" playbasis.php;

    cd ../models/tool/;
    sed -i.bak "/STREAM_URL/s/node.pbapp.net/'. \$_SERVER['NODE_STREAM_URL'] .'/" node_stream.php;
fi

exec "$@"