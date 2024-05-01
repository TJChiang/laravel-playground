FROM php:8.3-fpm

LABEL authors="TJChiang"

WORKDIR /source

ENV BUILD_DEPS \
    libcurl4-openssl-dev \
    libgmp-dev \
    libssl-dev \
    libxml2-dev \
    pkg-config \
    rsyslog \
    libpq-dev \
    libsqlite3-dev

RUN set -xe && \
        apt-get update && \
        apt-get install --yes --no-install-recommends --no-install-suggests \
            libpq5 \
            ${BUILD_DEPS} \
        && \
            docker-php-ext-install \
                gmp \
                soap \
                sockets \
                pcntl \
                pdo_mysql \
                pdo_pgsql \
                pdo_sqlite \
        && \
            pecl install \
                mongodb-1.18.1 \
                redis-6.0.2 \
        && \
            docker-php-ext-enable \
                mongodb \
                redis \
        && \
            apt-get remove --purge -y ${BUILD_DEPS} && \
            apt-get autoremove --purge -y && \
            rm -r /var/lib/apt/lists/* && \
            php -m
