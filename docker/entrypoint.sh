#!/bin/bash

set -e

echo "**** 1/10 - Make sure /uploads folders exist ****"
[ ! -f /uploads ] && \
	mkdir -p /uploads

echo "**** 2/10 - Create the symbolic link for the /uploads folder ****"
[ ! -L /var/www/koillection/public/uploads ] && \
	cp -r /var/www/koillection/public/uploads/. /uploads && \
	rm -r /var/www/koillection/public/uploads && \
	ln -s /uploads /var/www/koillection/public/uploads

echo "**** 3/10 - Setting env variables ****"
rm -rf /var/www/koillection/.env.local
touch /var/www/koillection/.env.local

echo "APP_ENV=${APP_ENV:-prod}" >> "/var/www/koillection/.env.local"
echo "APP_DEBUG=${APP_DEBUG:-0}" >> "/var/www/koillection/.env.local"
echo "APP_SECRET=${APP_SECRET:-$(openssl rand -base64 21)}" >> "/var/www/koillection/.env.local"

echo "JWT_SECRET_KEY=${JWT_SECRET_KEY:-%kernel.project_dir%/config/jwt/private.pem}" >> "/var/www/koillection/.env.local"
echo "JWT_PUBLIC_KEY=${JWT_PUBLIC_KEY:-%kernel.project_dir%/config/jwt/public.pem}" >> "/var/www/koillection/.env.local"
echo "JWT_PASSPHRASE=${JWT_PASSPHRASE:-$(openssl rand -base64 21)}" >> "/var/www/koillection/.env.local"

echo "DB_DRIVER=${DB_DRIVER:-}" >> "/var/www/koillection/.env.local"
echo "DB_NAME=${DB_NAME:-}" >> "/var/www/koillection/.env.local"
echo "DB_HOST=${DB_HOST:-}" >> "/var/www/koillection/.env.local"
echo "DB_PORT=${DB_PORT:-}" >> "/var/www/koillection/.env.local"
echo "DB_USER=${DB_USER:-}" >> "/var/www/koillection/.env.local"
echo "DB_PASSWORD=${DB_PASSWORD:-}" >> "/var/www/koillection/.env.local"
echo "DB_VERSION=${DB_VERSION:-}" >> "/var/www/koillection/.env.local"

echo "CORS_ALLOW_ORIGIN=${CORS_ALLOW_ORIGIN:-'^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'}" >> "/var/www/koillection/.env.local"

echo "session.cookie_secure=${HTTPS_ENABLED}" >> /etc/php/8.2/fpm/conf.d/php.ini
echo "date.timezone=${PHP_TZ}" >> /etc/php/8.2/fpm/conf.d/php.ini
echo "memory_limit=${PHP_MEMORY_LIMIT:-'512M'}" >> /etc/php/8.2/fpm/conf.d/php.ini

echo "upload_max_filesize=${UPLOAD_MAX_FILESIZE:-'20M'}" >> /etc/php/8.2/fpm/conf.d/php.ini
echo "post_max_size=${UPLOAD_MAX_FILESIZE:-'100M'}" >> /etc/php/8.2/fpm/conf.d/php.ini
sed -i "s/client_max_body_size 100M;/client_max_body_size ${UPLOAD_MAX_FILESIZE:-'100M'};/g" /etc/nginx/nginx.conf

echo "**** 4/10 - Migrate the database ****"
cd /var/www/koillection && \
php bin/console doctrine:migration:migrate --no-interaction --allow-no-migration --env=prod

echo "**** 5/10 - Refresh cached values ****"
cd /var/www/koillection && \
php bin/console app:refresh-cached-values --env=prod

echo "**** 6/10 - Create API keys ****"
cd /var/www/koillection && \
php bin/console lexik:jwt:generate-keypair --overwrite --env=prod

echo "**** 7/10 - Set Permissions ****" && \
chown -R www-data:www-data /var/www/koillection

echo "**** 8/10 - Create nginx log files ****" && \
mkdir -p /logs/nginx
chown -R "$USER":"$USER" /logs/nginx

echo "**** 9/10 - Create symfony log files ****" && \
[ ! -f /var/www/koillection/var/log ] && \
	mkdir -p /var/www/koillection/var/log

[ ! -f /var/www/koillection/var/log/prod.log ] && \
	touch /var/www/koillection/var/log/prod.log

chown -R www-data:www-data /var/www/koillection/var

echo "**** 10/10 - Setup complete, starting the server. ****"
php-fpm8.2
exec $@

echo "**** All done ****"