<p align="center">
    <a href="https://benjaminjonard.github.io/koillection/" target="_blank">
        <img src="https://avatars3.githubusercontent.com/u/38983306?s=200&v=4" width="75" height="75">
    </a>
</p>

<p align="center">
    <img src="https://img.shields.io/github/license/benjaminjonard/koillection" />
    <img src="https://img.shields.io/github/v/release/benjaminjonard/koillection" />
    <img src="https://img.shields.io/packagist/php-v/benjaminjonard/koillection" />    
    <img src="https://img.shields.io/scrutinizer/g/benjaminjonard/koillection" />
    <img src="https://img.shields.io/travis/benjaminjonard/koillection/master" />    
</p>

# Koillection

Koillection is a self-hosted service allowing users to manage any kind of collection.
It is a project I did for my personal use. But since it can interest some people I decided to release it publicly. 

## Warning

Koillection is still in development. There might be some bugs, missing features and changes in the future.

## Requirements

1. `PHP 7.4` You may need to add the following extensions:
    - `apcu`
    - `cgi`
    - `ctype`
    - `curl`
    - `dom`   
    - `exif`
    - `fileinfo`
    - `fpm`
    - `gd`
    - `iconv`
    - `intl`
    - `json`
    - `mbstring`    
    - `opcache`    
    - `openssl`
    - `pdo`    
    - `pdo_pgsql`    
    - `phar`
    - `session`
    - `simplexml`
    - `sodium`
    - `tokenizer`
    - `xml`
    - `xmlwriter`    
    - `zip`              
2. A webserver such as `Apache2` or `nginx` 
3. A `Postgresql` database

## Installation
### Using git or an archive file

1. `git clone` the repository or download and unzip the project archive
2. Create a `.env.local` file and copy the content of `.env` in it
3. In `.env.local` replace the values by your configuration and remove all curly braces
    - `APP_ENV` -> Symfony environment, prod by default
    - `APP_DEBUG` -> activate Symfony debug mode, 0 or 1
    - `APP_SECRET` -> a random string
    - `DB_USER` -> your database user
    - `DB_PASSWORD` -> your database password
    - `DB_HOST` -> your database address (ex: 127.0.0.1 or localhost)
    - `DB_PORT` -> your database port (5432 by default for postgres)
    - `DB_NAME` -> your database name
    - `DB_VERSION` -> your postgres server version (ex: 10.3)    
    - `PHP_TZ` -> Your timezone (ex: Europe/Paris)
4. In the project root folder execute `composer install -o`
5. And then `php bin/console doctrine:migrations:migrate`
6. Configure a vhost (you can find an example for nginx in `docs` folder)
7. (optionnal) Copy the values contained in `docs/php.ini` in your own `php.ini`. Not mandatory but can improve performance greatly 

### Using Docker
* https://github.com/benjaminjonard/koillection-docker ->  Comes with PHP FPM and nginx, based on the last release of Koillection.

## Licensing

Koillection is an Open Source software, released under the MIT License. 
