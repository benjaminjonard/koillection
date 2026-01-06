FROM ubuntu:jammy AS koillection-base

ARG DEBIAN_FRONTEND=noninteractive

# Environment variables
ENV APP_ENV='prod'
ENV PUID='1001'
ENV PGID='1001'
ENV USER='koillection'
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY ./ /var/www/koillection

# Install some basics dependencies
RUN set -eux ; \
    # Add User and Group
    addgroup --gid "$PGID" "$USER" ; \
    adduser --gecos '' --no-create-home --disabled-password --uid "$PUID" --gid "$PGID" "$USER" ; \
    # Prepare apt for php installation
    apt-get update -qq ; \
    apt-get install -qqy \
    --no-install-recommends \
    gnupg2 \
    software-properties-common \
    ; \
    # PHP
    add-apt-repository ppa:ondrej/php ; \
    # Install packages
    apt-get update -qq ; \
    apt-get install -qqy \
    --no-install-recommends \
    ca-certificates \
    curl \
    git \
    libnss3 \
    nginx-light \
    nss-plugin-pem \
    openssl \
    php8.4 \
    php8.4-apcu \
    php8.4-curl \
    php8.4-fpm \
    php8.4-gd \
    php8.4-intl \
    php8.4-mbstring \
    php8.4-mysql \
    php8.4-pgsql \
    php8.4-xml \
    php8.4-zip \
    unzip \
    ; \
    #Install composer dependencies
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer ; \
    cd /var/www/koillection ; \
    COMPOSER_MEMORY_LIMIT=-1 composer install --classmap-authoritative ; \
    COMPOSER_MEMORY_LIMIT=-1 composer clearcache ; \
    # Dump translation files for javascript
    cd /var/www/koillection/ ; \
    php bin/console app:translations:dump ; \
    # Clean up
    apt-get purge -y \
    ca-certificates \
    git \
    gnupg2 \
    software-properties-common \
    unzip \
    ; \
    apt-get autoremove -y ; \
    apt-get clean ; \
    rm -rf /var/lib/apt/lists/* ; \
    rm -rf /usr/local/bin/composer ; \
    # Set permissions \
    sed -i "s/user = www-data/user = $USER/g" /etc/php/8.4/fpm/pool.d/www.conf ; \
    sed -i "s/group = www-data/group = $USER/g" /etc/php/8.4/fpm/pool.d/www.conf ; \
    chown -R "$USER":"$USER" /var/www/koillection ; \
    chmod +x /var/www/koillection/docker/entrypoint.sh ; \
    # Add nginx and PHP config files
    cp /var/www/koillection/docker/default.conf /etc/nginx/nginx.conf ; \
    cp /var/www/koillection/docker/php.ini /etc/php/8.4/fpm/conf.d/php.ini ; \
    mkdir /run/php

FROM node:21-bookworm AS build-node

WORKDIR /app

COPY ./assets/ ./assets

COPY --from=koillection-base /var/www/koillection/assets/js/translations /app/assets/js/translations

WORKDIR /app/assets

RUN set -eux ; \
    mkdir -p /app/public/build/ ; \
    corepack enable ; \
    yarn --version ; \
    yarn install ; \
    yarn build ;

FROM curlimages/curl:8.17.0 AS download-env

# renovate: datasource=github-releases depName=lwthiker/curl-impersonate packageName=lwthiker/curl-impersonate
ENV CURL_IMPERSONATE_VERSION="0.6.1"

WORKDIR /opt

USER root

RUN set -eux ; \
    # Determine architecture
    ARCHITECTURE="$(uname -m)" ; \
    case $ARCHITECTURE in \
    x86_64) ARCHITECTURE="x86_64" ;; \
    aarch64 | armv8* | arm64) ARCHITECTURE="aarch64" ;; \
    *) \
    echo "(!) Architecture $ARCHITECTURE unsupported" \
    exit 1 \
    ;; \
    esac ;\
    FILE_NAME="libcurl-impersonate-v${CURL_IMPERSONATE_VERSION}.${ARCHITECTURE}-linux-gnu.tar.gz" ; \
    curl \
    --fail \
    --location \
    --output /tmp/${FILE_NAME} \
    --show-error \
    --silent \
    "https://github.com/lwthiker/curl-impersonate/releases/download/v${CURL_IMPERSONATE_VERSION}/${FILE_NAME}" \
    ; \
    tar xvzf /tmp/${FILE_NAME} -C /opt/

FROM koillection-base AS koillection-final

COPY --from=build-node /app/public/build/ /var/www/koillection/public/build/
COPY --from=download-env /opt/libcurl-impersonate* /opt/

EXPOSE 80

VOLUME /uploads

WORKDIR /var/www/koillection

HEALTHCHECK CMD curl --fail http://localhost:80/ || exit 1

ENTRYPOINT ["sh", "/var/www/koillection/docker/entrypoint.sh" ]

CMD [ "nginx" ]
