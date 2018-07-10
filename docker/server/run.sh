#!/bin/sh

chown -R "${UID}:${GID}" /etc/s6.d /var/log/nginx /var/log/php7 /var/tmp/nginx /certs /etc/php7 /etc/nginx /run/nginx
exec su-exec "${UID}:${GID}" /bin/s6-svscan /etc/s6.d
