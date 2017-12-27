FROM alpine:3.7

ARG PHP_EXT
ENV UID=791 GID=791

COPY s6.d /etc/s6.d
COPY run.sh /usr/local/bin/run.sh 
COPY php-fpm.conf /etc/php7/php-fpm.conf
COPY nginx /etc/nginx

# install core
RUN set -xe \
    && apk add --no-cache -U s6 su-exec ca-certificates nginx php7-fpm $PHP_EXT \
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
