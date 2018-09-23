FROM alpine:3.8
RUN apk add --no-cache -U su-exec tini s6
ENTRYPOINT ["/sbin/tini", "--"]

ENV UID=791 GID=791
ENV MAIL=mail@example.com

VOLUME ["/elzire/data"]
EXPOSE 8080 8443
WORKDIR /elzire
COPY parameters.yml /elzire/data/parameters.yml

RUN set -xe \
    && apk add --no-cache openssl nginx php7-fpm imagemagick php7-common php7-imagick php7-mbstring php7-gd php7-intl php7-iconv php7-json php7-dom php7-ctype php7-xml php7-posix php7-session php7-tokenizer php7-fileinfo php7-openssl php7 php7-simplexml php7-apcu \
    && apk add --no-cache --virtual .build-deps wget unzip composer yarn ca-certificates \
    && wget -q https://github.com/nrobinaubertin/elzire.fr/archive/master.zip \
    && unzip master.zip \
    && find elzire.fr-master -mindepth 1 -maxdepth 1 -print0 | xargs -0 -i mv {} /elzire \
    && rmdir elzire.fr-master \
    && rm master.zip \
    && composer install \
    && cd web && yarn && yarn build && rm -rf node_modules && cd /elzire \
    && apk del .build-deps \
    && mkdir -p /run/nginx

COPY s6.d /etc/s6.d
COPY php-fpm.conf /etc/php7/php-fpm.conf
COPY nginx /etc/nginx
COPY run.sh /usr/local/bin/run.sh

RUN chmod -R +x /usr/local/bin /etc/s6.d /var/lib/nginx /run/nginx

CMD ["run.sh"]
