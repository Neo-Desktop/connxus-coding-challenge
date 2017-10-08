#! /bin/sh

apk update
apk add build-base autoconf
PHP_AUTOCONF=/usr/bin/autoconf
pecl install xdebug-2.5.4
docker-php-ext-enable xdebug
docker-php-ext-install pdo pdo_mysql
apk del build-base
exec php-fpm