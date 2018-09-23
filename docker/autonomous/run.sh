#!/bin/sh

php bin/console cache:clear --env=prod --no-debug --no-warmup
chown -R "${UID}:${GID}" /etc/s6.d /var/log /var/tmp/nginx /etc/php7 /etc/nginx /run/nginx /elzire
exec su-exec "${UID}:${GID}" /bin/s6-svscan /etc/s6.d
