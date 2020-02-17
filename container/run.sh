#!/bin/sh

set -xe

# Remove event directories that can cause fails like:
# s6-supervise <service name>: fatal: unable to mkfifodir event: Permission denied
rm -rf $(find /etc/s6.d -name 'event')

rm -rf /elzire/var/*
php bin/console cache:clear --env=prod --no-debug
mkdir -p /elzire/data/cache/thumbs
php bin/console elzire:gen-thumbs &
exec /bin/s6-svscan /etc/s6.d
