ARG php_cli_image
FROM $php_cli_image AS php-common

ENV PHP_EXT_DIR /usr/local/lib/php/extensions/no-debug-non-zts-20210902
RUN set -ex \
    && if [ `pear config-get ext_dir` != ${PHP_EXT_DIR} ]; then echo PHP_EXT_DIR must be `pear config-get ext_dir` && exit 1; fi

FROM php-common AS php-build
RUN --mount=type=cache,target=/var/cache/apk set -ex \
    && apk add --update-cache $PHPIZE_DEPS

FROM php-build AS php-ext-intl
RUN --mount=type=cache,target=/var/cache/apk set -ex \
    && apk add \
        icu-dev \
	&& docker-php-ext-install intl

FROM php-build AS php-ext-bcmath
RUN --mount=type=cache,target=/var/cache/apk set -ex \
    && docker-php-ext-install bcmath

FROM php-build AS php-ext-memcached
RUN --mount=type=cache,target=/var/cache/apk set -ex \
    && apk add \
        libzip-dev \
        libmemcached-dev \
    && pecl install memcached

FROM php-build AS php-ext-pdo
RUN --mount=type=cache,target=/var/cache/apk set -ex \
    && apk add \
        postgresql-dev \
    && docker-php-ext-install pdo_pgsql

FROM php-build AS php-ext-pcntl
RUN set -ex \
    && docker-php-ext-install pcntl

FROM php-build AS php-ext-sockets
RUN set -ex \
    && docker-php-ext-install sockets

FROM php-build AS php-ext-amqp
RUN set -ex \
    && apk add \
        rabbitmq-c-dev \
    && pecl install amqp-1.11.0beta


FROM php-build AS php-ext-gd
RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

FROM php-build AS php-ext-zip
RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add \
        libzip-dev \
    && docker-php-ext-install zip



FROM php-common AS php-base
COPY --from=php-ext-pdo ${PHP_EXT_DIR}/pdo_pgsql.so ${PHP_EXT_DIR}/
COPY --from=php-ext-intl ${PHP_EXT_DIR}/intl.so ${PHP_EXT_DIR}/
COPY --from=php-ext-intl /usr/local /usr/local
COPY --from=php-ext-pcntl ${PHP_EXT_DIR}/pcntl.so ${PHP_EXT_DIR}/
COPY --from=php-ext-bcmath ${PHP_EXT_DIR}/bcmath.so ${PHP_EXT_DIR}/
COPY --from=php-ext-memcached ${PHP_EXT_DIR}/memcached.so ${PHP_EXT_DIR}/
COPY --from=php-ext-sockets ${PHP_EXT_DIR}/sockets.so ${PHP_EXT_DIR}/
COPY --from=php-ext-amqp ${PHP_EXT_DIR}/amqp.so ${PHP_EXT_DIR}/
COPY --from=php-ext-gd ${PHP_EXT_DIR}/gd.so ${PHP_EXT_DIR}/
COPY --from=php-ext-zip ${PHP_EXT_DIR}/zip.so ${PHP_EXT_DIR}/

RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add \
        libmemcached \
        libpq \
        icu \
        rabbitmq-c \
        libpng \
        libjpeg-turbo \
        freetype \
        libzip \
        postgresql-client \
        shadow \
        supervisor \
        gettext \
    && docker-php-ext-enable \
        intl \
        memcached \
        pdo_pgsql \
        bcmath \
        pcntl \
        sockets \
        gd \
        amqp \
        zip

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

ARG user
ARG uid

RUN addgroup $user && \
    adduser -DSH -u $uid -G $user $user && \
    adduser www-data $user

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./dev/php/conf.d /usr/local/etc/php/conf.d
COPY ./dev/php-cli/conf.d /usr/local/etc/php/conf.d

RUN rm -rf /var/lib/apt/lists/*


COPY --chown=$user:$user ./common/supervisor/supervisord.conf /var/tmp/supervisord.conf
COPY --chown=$user:$user ./common/supervisor/conf.d/messenger.conf /var/tmp/messenger.conf
RUN mkdir -p /etc/supervisord/conf.d && \
    /bin/sh -c envsubst < /var/tmp/supervisord.conf > /etc/supervisord/supervisord.conf && \
    /bin/sh -c envsubst < /var/tmp/messenger.conf > /etc/supervisord/conf.d/messenger.conf

COPY --chown=$user:$user ./common/supervisor/eventconsumer.sh /usr/local/eventconsumer.sh
COPY --chown=$user:$user ./common/supervisor/pre_stop.sh /usr/local/pre_stop.sh

WORKDIR /etc/supervisord
