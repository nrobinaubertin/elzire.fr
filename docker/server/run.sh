#!/bin/sh

#crontab -r
#echo "@monthly certbot certonly -n --agree-tos --webroot -w /var/lib/letsencrypt --force-renewal --email $MAIL -d $DOMAIN --cert-name elzire --cert-path /certs/cert.pem --key-path /certs/privkey.pem --fullchain-path /certs/fullchain.pem --chain-path /certs/cert.pem" | crontab -

# mkdir -p /letsencrypt/.well-known/acme-challenge
# echo "plop" > /letsencrypt/.well-known/acme-challenge/plop

chown -R "${UID}:${GID}" /php /nginx /etc/s6.d /var/log/nginx /var/tmp/nginx /certs
exec su-exec "${UID}:${GID}" /bin/s6-svscan /etc/s6.d
