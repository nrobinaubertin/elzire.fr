FROM alpine:3.7

ENV UID=791 GID=791

COPY s6.d /etc/s6.d
COPY run.sh /usr/local/bin/run.sh 
COPY php-fpm.conf /etc/php7/php-fpm.conf
COPY nginx /etc/nginx

# install core
RUN set -xe \
    && apk add --no-cache -U s6 su-exec ca-certificates nginx php7-fpm \
    && apk add --no-cache imagemagick php7-common php7-imagick php7-mbstring php7-gd php7-intl php7-iconv php7-json php7-dom php7-ctype php7-xml php7-posix php7-session php7-tokenizer php7-fileinfo php7-openssl \
    && mkdir /www \
    && mkdir -p /php/logs \
    && mkdir -p /nginx/logs \
    && mkdir -p /nginx/tmp/client_body \
    && mkdir -p /nginx/tmp/proxy \
    && mkdir -p /nginx/tmp/fastcgi \
    && mkdir -p /nginx/tmp/uwsgi \
    && mkdir -p /var/lib/nginx/logs \
    && chmod -R +x /usr/local/bin /etc/s6.d /var/lib/nginx

VOLUME /www
EXPOSE 8080

CMD ["run.sh"]
