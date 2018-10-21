#!/bin/sh

rm -rf /elzire/var/*
php bin/console cache:clear --env=prod --no-debug
mkdir -p /elzire/data/cache/thumbs
chown -R "${UID}:${GID}" /etc/s6.d /var/log /var/tmp/nginx /etc/php7 /etc/nginx /run/nginx /elzire
su-exec "${UID}:${GID}" php bin/console elzire:gen-thumbs &
exec su-exec "${UID}:${GID}" /bin/s6-svscan /etc/s6.d
