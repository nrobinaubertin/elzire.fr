FROM php:fpm-alpine

ARG IMAGICK_VERSION=3.4.3
ARG UID=82
ARG GID=82

RUN set -xe \
	&& apk add --no-cache freetype-dev libpng-dev libjpeg-turbo-dev libxml2-dev su-exec tini \
	&& apk add --no-cache --virtual .phpize-deps autoconf g++ imagemagick-dev libtool make \
    && docker-php-ext-configure gd \
        --with-gd \
        --with-freetype-dir=/usr/include/ \
        --with-png-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd mbstring opcache \
    && pecl install imagick-${IMAGICK_VERSION} \
    && docker-php-ext-enable imagick \
	&& apk add --no-cache imagemagick \
    && apk del .phpize-deps

RUN sed -ri "s/^www-data:x:82:82:/www-data:x:${UID}:${GID}:/" /etc/passwd

EXPOSE 9000
ENTRYPOINT ["/sbin/tini", "--"]
CMD ["php-fpm"]
