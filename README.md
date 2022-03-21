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
If you like Koillection please consider leaving a star, it gives additional motivation to continue working on the project.

## Warning

Please back up your database, especially when updating to a new version. I do my best to test new versions, especially when they contains data migrations but some edge cases may escape my vigilance.

Please do back up your database.

## Requirements

1. PHP

    | Koillection | PHP version | Maintained                |
    |-------------| ----------- | ---------                 |
    | 1.3.x       | 8.1         | :heavy_check_mark:        | 
    | 1.2.x       | 8.0         | :x:                       |
    | 1.1.x       | 7.4         | :x:                       |
    | 1.0.x       | 7.2         | :x:                       |

2. Make sure you have the following PHP extensions installed:
    - `ctype`, `fileinfo`, `gd`, `iconv`, `intl`, `json`, `apcu`
3. A webserver such as `Apache2` or `nginx` 
4. A `Postgresql` or `Mysql` (version 8 or superior) database
5. Yarn

## Installation
### Using git or an archive file

1. `git clone` the repository or download and unzip the project archive
2. Create a `.env.local` file and copy the content of `.env` in it
3. In `.env.local` replace the values by your configuration and remove all curly braces
    - `APP_ENV` -> Symfony environment, prod by default
    - `APP_DEBUG` -> activate Symfony debug mode, 0 or 1
    - `DB_DRIVER` -> pdo_mysql or pdo_pgsql
    - `DB_USER` -> your database user
    - `DB_PASSWORD` -> your database password
    - `DB_HOST` -> your database address (ex: 127.0.0.1 or localhost)
    - `DB_PORT` -> your database port (5432 by default for postgres, 3306 for mysql)
    - `DB_NAME` -> your database name
    - `DB_VERSION` -> your postgres server version (ex: 10.3)    
    - `PHP_TZ` -> Your timezone (ex: Europe/Paris)
4. In the project root folder execute `bin/composer install --classmap-authoritative`
5. Then `php bin/console doctrine:migrations:migrate`
6. (Optional) Generate SSH keys for API `php bin/console lexik:jwt:generate-keypair`  (not mandatory if you do not plan on using the API)
7. Configure a vhost (you can find an example for nginx in `docs` folder)
8. Generate assets : `cd assets/ && yarn install && yarn build && cd ..`
9. (Optional) Copy the values contained in `docs/php.ini` in your own `php.ini`. Not mandatory but can improve performance greatly

### Using Docker
* https://github.com/koillection/koillection-docker ->  Comes with PHP FPM and nginx, based on the last release of Koillection.

## Updating

Please backup your database and /uploads folder before updating

### Using git or an archive file
1. In the project root folder execute `bin/composer install --classmap-authoritative`
2. Then `php bin/console doctrine:migrations:migrate`

### Using Docker
Just pulling the new image and restarting the container should be enough

## Licensing
Koillection is an Open Source software, released under the MIT License. 

## API
You can access a basic REST API documentation on /api.

To use it you need get a JWT token using your username and your password by calling
```
POST /api/authentication_token
{
   "username": "johndoe",
   "password": "password"
}
```
Then for every requests to the API, add the following header :
```
Authorization: Bearer the_jwt_token
```

### Known limitations
1. No access to admin features
2. No access to sharing features (you can't see someone else content)
3. Uploads only work for POST requests