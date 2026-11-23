#!/bin/bash

set -e

MISSING_VARS=""
for VAR in APP_SECRET JWT_PASSPHRASE DB_DRIVER DB_NAME DB_HOST DB_PORT DB_USER DB_PASSWORD DB_VERSION; do
	if [ -z "$(printenv "$VAR")" ]; then
		MISSING_VARS="$MISSING_VARS $VAR"
	fi
done

if [ -n "$MISSING_VARS" ]; then
	echo "ERROR: the following mandatory environment variables are not set:$MISSING_VARS"
	exit 1
fi

echo "**** 1/8 - Setting env variables ****"
rm -rf /app/public/.env.local
touch /app/public/.env.local

echo "APP_ENV=${APP_ENV:-prod}" >> "/app/public/.env.local"
echo "APP_DEBUG=${APP_DEBUG:-0}" >> "/app/public/.env.local"
echo "APP_SECRET=${APP_SECRET:-}" >> "/app/public/.env.local"

echo "JWT_SECRET_KEY=${JWT_SECRET_KEY:-%kernel.project_dir%/config/jwt/private.pem}" >> "/app/public/.env.local"
echo "JWT_PUBLIC_KEY=${JWT_PUBLIC_KEY:-%kernel.project_dir%/config/jwt/public.pem}" >> "/app/public/.env.local"
echo "JWT_PASSPHRASE=${JWT_PASSPHRASE:-}" >> "/app/public/.env.local"

echo "DB_DRIVER=${DB_DRIVER:-}" >> "/app/public/.env.local"
echo "DB_NAME=${DB_NAME:-}" >> "/app/public/.env.local"
echo "DB_HOST=${DB_HOST:-}" >> "/app/public/.env.local"
echo "DB_PORT=${DB_PORT:-}" >> "/app/public/.env.local"
echo "DB_USER=${DB_USER:-}" >> "/app/public/.env.local"
echo "DB_PASSWORD=${DB_PASSWORD:-}" >> "/app/public/.env.local"
echo "DB_VERSION=${DB_VERSION:-}" >> "/app/public/.env.local"

echo "CORS_ALLOW_ORIGIN=${CORS_ALLOW_ORIGIN:-'^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'}" >> "/app/public/.env.local"


echo "session.cookie_secure=${HTTPS_ENABLED}" >> /usr/local/etc/php/conf.d/php.ini
echo "date.timezone=${PHP_TZ}" >> /usr/local/etc/php/conf.d/php.ini
echo "memory_limit=${PHP_MEMORY_LIMIT:-'512M'}" >> /usr/local/etc/php/conf.d/php.ini

echo "upload_max_filesize=${UPLOAD_MAX_FILESIZE:-'20M'}" >> /usr/local/etc/php/conf.d/php.ini
echo "post_max_size=${UPLOAD_MAX_FILESIZE:-'100M'}" >> /usr/local/etc/php/conf.d/php.ini

echo "opcache.enable=0" >> /usr/local/etc/php/conf.d/php.ini

echo "**** 2/8 - Migrate the database ****"
cd /app/public && \
php bin/console doctrine:migration:migrate --no-interaction --allow-no-migration

echo "**** 3/8 - Refresh cached values ****"
cd /app/public && \
php bin/console app:refresh-cached-values

echo "**** 4/8 - Create API keys ****"
cd /app/public && \
php bin/console lexik:jwt:generate-keypair --skip-if-exists

echo "**** 5/8 - Create user and use PUID/PGID ****"
PUID=${PUID:-1000}
PGID=${PGID:-1000}
if [ ! "$(id -u "$USER")" -eq "$PUID" ]; then usermod -o -u "$PUID" "$USER" ; fi
if [ ! "$(id -g "$USER")" -eq "$PGID" ]; then groupmod -o -g "$PGID" "$USER" ; fi
echo -e " \tUser UID :\t$(id -u "$USER")"
echo -e " \tUser GID :\t$(id -g "$USER")"

echo "**** 6/8 - Set Permissions ****" && \
find /app/public/public/uploads -type d \( ! -user "$USER" -o ! -group "$USER" \) -exec chown -R "$USER":"$USER" \{\} \;
find /app/public/public/uploads \( ! -user "$USER" -o ! -group "$USER" \) -exec chown "$USER":"$USER" \{\} \;
usermod -a -G "$USER" www-data
find /app/public/public/uploads -type d \( ! -perm -ug+w -o ! -perm -ugo+rX \) -exec chmod -R ug+w,ugo+rX \{\} \;
find /app/public/public/uploads \( ! -perm -ug+w -o ! -perm -ugo+rX \) -exec chmod ug+w,ugo+rX \{\} \;

echo "**** 7/8 - Create symfony log files ****" && \
[ ! -f /app/public/var/log ] && \
	mkdir -p /app/public/var/log

[ ! -f /app/public/var/log/prod.log ] && \
	touch /app/public/var/log/prod.log

chown -R "$USER":"$USER" /app/public/var/log
chown -R "$USER":"$USER" /app/public/var/log/prod.log
chown -R "$USER":"$USER" /app/public/.env.local

echo "**** 8/8 - Setup complete, starting the server and the workers. ****"
supervisord -c /app/public/docker/supervisord.conf
exec "$@"

echo "**** All done ****"