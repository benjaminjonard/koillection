<p align="center">
    <a href="https://koillection.github.io/" target="_blank">
        <img src="https://user-images.githubusercontent.com/20560781/80213166-0e560e00-8639-11ea-944e-4f79fdbcef55.png" width="75" height="75">
    </a>
</p>

<p align="center">
<img src="https://img.shields.io/github/license/koillection/koillection" />    
    <img src="https://img.shields.io/github/v/release/koillection/koillection" />
    <img src="https://img.shields.io/travis/koillection/koillection/master" />
    <img src="https://img.shields.io/scrutinizer/g/koillection/koillection" />    
</p>
<p align="center">
    <img src="https://img.shields.io/packagist/php-v/koillection/koillection" />    
    <img src="https://img.shields.io/badge/mysql-^8.0-blue" />
    <img src="https://img.shields.io/badge/postgresql-^10.0-blue" />    
<p>

# Koillection

Koillection is a self-hosted service allowing users to manage any kind of collection.
It is a project I did for my personal use. But since it can interest some people I decided to release it publicly. 

## Warning

Koillection is still in development. There might be some bugs, missing features and changes in the future.

## Requirements

1. PHP

    | Koillection | PHP version | Maintened                |
    | ------------| ----------- | ---------                |
    | 1.1.x       | 7.4         | :heavy_check_mark:       |
    | 1.0.x       | 7.2         | :x:                      |

2. You may need to add the following extensions:
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
    - `pdo_mysql`    
    - `phar`
    - `session`
    - `simplexml`
    - `sodium`
    - `tokenizer`
    - `xml`
    - `xmlwriter`    
    - `zip`              
3. A webserver such as `Apache2` or `nginx` 
4. A `Postgresql` or `Mysql` (version 8 or superior) database

## Installation
### Using git or an archive file

1. `git clone` the repository or download and unzip the project archive
2. Create a `.env.local` file and copy the content of `.env` in it
3. In `.env.local` replace the values by your configuration and remove all curly braces
    - `APP_ENV` -> Symfony environment, prod by default
    - `APP_DEBUG` -> activate Symfony debug mode, 0 or 1
    - `APP_SECRET` -> a random string
    - `DB_DRIVER` -> pdo_mysql or pdo_pgsql
    - `DB_USER` -> your database user
    - `DB_PASSWORD` -> your database password
    - `DB_HOST` -> your database address (ex: 127.0.0.1 or localhost)
    - `DB_PORT` -> your database port (5432 by default for postgres, 3306 for mysql)
    - `DB_NAME` -> your database name
    - `DB_VERSION` -> your postgres server version (ex: 10.3)    
    - `PHP_TZ` -> Your timezone (ex: Europe/Paris)
4. In the project root folder execute `composer install -o`
5. Then `php bin/console doctrine:migrations:migrate`
6. Configure a vhost (you can find an example for nginx in `docs` folder)
7. (optionnal) Copy the values contained in `docs/php.ini` in your own `php.ini`. Not mandatory but can improve performance greatly 

### Using Docker
* https://github.com/koillection/koillection-docker ->  Comes with PHP FPM and nginx, based on the last release of Koillection.

## Licensing

Koillection is an Open Source software, released under the MIT License. 
