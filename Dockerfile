FROM alpine:3.11
RUN apk add --no-cache -U su-exec tini s6
ENTRYPOINT ["/sbin/tini", "--"]

ENV MAIL=mail@domain.example

EXPOSE 8080
WORKDIR /elzire

COPY container/parameters.yml /elzire/data/parameters.yml
COPY app /elzire/app
COPY web /elzire/web
COPY src /elzire/src
COPY bin /elzire/bin
COPY composer.json /elzire/composer.json
COPY composer.lock /elzire/composer.lock

RUN set -xe \
    && mkdir -p /run/nginx /elzire/var \
    && apk add --no-cache openssl nginx php7-fpm imagemagick php7-common php7-imagick php7-mbstring php7-gd php7-intl php7-iconv php7-json php7-dom php7-ctype php7-xml php7-posix php7-session php7-tokenizer php7-fileinfo php7-openssl php7 php7-simplexml php7-apcu \
    && apk add --no-cache --virtual .build-deps wget unzip composer yarn ca-certificates \
    && php bin/composer install --prefer-dist \
    && cd web && yarn && yarn build && cd /elzire \
    && apk del .build-deps

COPY container/s6.d /etc/s6.d
COPY container/php-fpm.conf /etc/php7/php-fpm.conf
COPY container/nginx /etc/nginx
COPY container/run.sh /usr/local/bin/run.sh

RUN chmod -R +x /usr/local/bin /etc/s6.d /var/lib/nginx /run/nginx
VOLUME ["/elzire/data"]

CMD ["run.sh"]
