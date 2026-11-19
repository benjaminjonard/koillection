#!/bin/bash

set -e

if [ "$(id -u)" -eq 0 ]; then
    RUN_AS_USER="setpriv --reuid $USER --regid $USER --init-groups"
else
    RUN_AS_USER=""
fi

echo "**** 1/9 - Make sure /uploads folders exist ****"
[ ! -f /uploads ] && \
	mkdir -p /uploads

echo "**** 2/9 - Create the symbolic link for the /uploads folder ****"
[ ! -L /app/public/public/uploads ] && \
	cp -r /app/public/public/uploads/. /uploads && \
	rm -r /app/public/public/uploads && \
	ln -s /uploads /app/public/public/uploads

echo "**** 3/9 - Setting env variables ****"
rm -rf /app/public/.env.local
touch /app/public/.env.local

echo "APP_ENV=${APP_ENV:-prod}" >> "/app/public/.env.local"
echo "APP_DEBUG=${APP_DEBUG:-0}" >> "/app/public/.env.local"
echo "APP_SECRET=${APP_SECRET:-$(openssl rand -base64 21)}" >> "/app/public/.env.local"

echo "JWT_SECRET_KEY=${JWT_SECRET_KEY:-%kernel.project_dir%/config/jwt/private.pem}" >> "/app/public/.env.local"
echo "JWT_PUBLIC_KEY=${JWT_PUBLIC_KEY:-%kernel.project_dir%/config/jwt/public.pem}" >> "/app/public/.env.local"
echo "JWT_PASSPHRASE=${JWT_PASSPHRASE:-$(openssl rand -base64 21)}" >> "/app/public/.env.local"

echo "DB_DRIVER=${DB_DRIVER:-}" >> "/app/public/.env.local"
echo "DB_NAME=${DB_NAME:-}" >> "/app/public/.env.local"
echo "DB_HOST=${DB_HOST:-}" >> "/app/public/.env.local"
echo "DB_PORT=${DB_PORT:-}" >> "/app/public/.env.local"
echo "DB_USER=${DB_USER:-}" >> "/app/public/.env.local"
echo "DB_PASSWORD=${DB_PASSWORD:-}" >> "/app/public/.env.local"
echo "DB_VERSION=${DB_VERSION:-}" >> "/app/public/.env.local"

echo "CORS_ALLOW_ORIGIN=${CORS_ALLOW_ORIGIN:-'^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'}" >> "/app/public/.env.local"

if [ "$(id -u)" -eq 0 ]; then
	echo "**** 4/9 - Create user and use PUID/PGID ****"
	PUID=${PUID:-1000}
	PGID=${PGID:-1000}
	if [ ! "$(id -u "$USER")" -eq "$PUID" ]; then usermod -o -u "$PUID" "$USER" ; fi
	if [ ! "$(id -g "$USER")" -eq "$PGID" ]; then groupmod -o -g "$PGID" "$USER" ; fi
	echo -e " \tUser UID :\t$(id -u "$USER")"
	echo -e " \tUser GID :\t$(id -g "$USER")"

	echo "**** 5/9 - Set Permissions ****"
	find /uploads -type d \( ! -user "$USER" -o ! -group "$USER" \) -exec chown -R "$USER":"$USER" \{\} \;
	find /uploads \( ! -user "$USER" -o ! -group "$USER" \) -exec chown "$USER":"$USER" \{\} \;
	usermod -a -G "$USER" www-data
	find /uploads -type d \( ! -perm -ug+w -o ! -perm -ugo+rX \) -exec chmod -R ug+w,ugo+rX \{\} \;
	find /uploads \( ! -perm -ug+w -o ! -perm -ugo+rX \) -exec chmod ug+w,ugo+rX \{\} \;
	mkdir -p /app/public/var/log /app/public/config/jwt
	chown -R "$USER":"$USER" /app/public/var /app/public/config/jwt /app/public/.env.local /data /config
	chown "$USER" /dev/stdout /dev/stderr
else
	echo "**** 4/9 - Running as UID $(id -u), skipping user creation ****"
	echo "**** 5/9 - Running as UID $(id -u), skipping permissions ****"
fi

echo "**** 6/9 - Migrate the database ****"
cd /app/public && \
$RUN_AS_USER php bin/console doctrine:migration:migrate --no-interaction --allow-no-migration

echo "**** 7/9 - Refresh cached values ****"
cd /app/public && \
$RUN_AS_USER php bin/console app:refresh-cached-values

echo "**** 8/9 - Create API keys ****"
cd /app/public && \
$RUN_AS_USER php bin/console lexik:jwt:generate-keypair --skip-if-exists

echo "**** 9/9 - Setup complete, starting the server and the workers. ****"
exec $RUN_AS_USER supervisord -c /app/public/docker/supervisord.conf
