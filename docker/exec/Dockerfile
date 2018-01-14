FROM alpine:3.7

ENV UID=791
ENV GID=791
ENV USER=exec
ENV GROUP=exec

VOLUME /exec
WORKDIR /exec

RUN set -xe \
    && apk add --no-cache -U su-exec php7 git zip unzip curl wget ca-certificates openssl bash nodejs-npm gnupg yarn sudo \
    && apk add --no-cache imagemagick php7 php7-apcu php7-common php7-ctype php7-curl php7-dom php7-fileinfo php7-gd php7-iconv php7-imagick php7-intl php7-json php7-mbstring php7-openssl php7-pdo_mysql php7-phar php7-posix php7-session php7-simplexml php7-tokenizer php7-xml php7-xmlwriter

COPY run.sh /usr/local/bin/run.sh
RUN chmod +x /usr/local/bin/run.sh

ENTRYPOINT ["run.sh"]
