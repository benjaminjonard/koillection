FROM dunglas/frankenphp:php8.5 AS koillection-base

# Environment variables
ENV APP_ENV=prod
ENV PUID=1001
ENV PGID=1001
ENV USER=koillection
ENV FRANKENPHP_CONFIG="worker /app/public/public/index.php"
ENV FRANKENPHP_SERVER_NAME=":80"
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY ./ /app/public
COPY ./docker/Caddyfile /etc/caddy/Caddyfile

# Install some basics dependencies
RUN set -eux ; \
    # Add User and Group \
    addgroup --gid "$PGID" "$USER" ; \
    adduser --gecos '' --no-create-home --disabled-password --uid "$PUID" --gid "$PGID" "$USER" ; \
    # Install packages \
    apt-get update -qq ; \
    apt-get install -qqy --no-install-recommends \
    curl \
    gnupg2 \
    libnss3 \
    nss-plugin-pem \
    ca-certificates \
    git \
    unzip \
    openssl ; \
    # Install PHP extensions \
    install-php-extensions opcache pdo_pgsql pdo_mysql intl gd zip apcu curl ; \
    #Install composer dependencies \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer ; \
    cd /app/public ; \
    COMPOSER_MEMORY_LIMIT=-1 composer install --classmap-authoritative ; \
    COMPOSER_MEMORY_LIMIT=-1 composer clearcache ; \
    # Dump translation files for javascript \
    cd /app/public ; \
    php bin/console app:translations:dump ; \
    # Clean up \
    apt-get purge -y git ca-certificates gnupg2 unzip ; \
    apt-get autoremove -y ; \
    apt-get clean ; \
    rm -rf /var/lib/apt/lists/* ; \
    rm -rf /usr/local/bin/composer ; \
    # Set permissions \
    chown -R "$USER":"$USER" /app/public ; \
    chmod +x /app/public/docker/entrypoint.sh ; \
    mkdir /run/php ; \
    # Add PHP config files \
    cp /app/public/docker/php.ini /usr/local/etc/php/conf.d/php.ini

FROM node:21-bookworm AS build-node

WORKDIR /app

COPY ./assets/ ./assets
COPY --from=koillection-base /app/public/assets/js/translations /app/assets/js/translations

WORKDIR /app/assets

RUN set -eux ; \
    mkdir -p /app/public/build/ ; \
    corepack enable ; \
    yarn --version ; \
    yarn install ; \
    yarn build ;

FROM curlimages/curl:8.18.0 AS download-env

# renovate: datasource=github-releases depName=lwthiker/curl-impersonate packageName=lwthiker/curl-impersonate
ENV CURL_IMPERSONATE_VERSION="0.6.1"

WORKDIR /opt

USER root

RUN set -eux ; \
    # Determine architecture \
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


# Install curl-impersonate
ADD https://github.com/lwthiker/curl-impersonate/releases/download/v0.6.1/libcurl-impersonate-v0.6.1.x86_64-linux-gnu.tar.gz /opt/
RUN cd /opt tar xvzf libcurl-impersonate-v0.6.1.x86_64-linux-gnu.tar.gz rm libcurl-impersonate-v0.6.1.x86_64-linux-gnu.tar.gz

FROM koillection-base AS koillection-final

COPY --from=build-node /app/public/build/ /app/public/public/build/
COPY --from=download-env /opt/libcurl-impersonate* /opt/

EXPOSE 80
EXPOSE 443

WORKDIR /app/public

HEALTHCHECK CMD curl --fail http://localhost:80/ || exit 1

ENTRYPOINT ["sh", "/app/public/docker/entrypoint.sh" ]