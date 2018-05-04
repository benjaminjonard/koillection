# Koillection

Koillection is a self-hosted service allowing users to manage any kind of collection.
It is a project I did for my personal use. But since it can interest some people I decided to release it publicly. 

## Warning

Koillection is still in development. There might be some bugs, missing features and changes in the future.

## Requirements

1. `PHP 7.2`
2. `A webserver such as Apache2 or nginx` 
3. `A Postgresql database`

## Installation

1. Copy the `.env.dist` file and rename it `.env`
2. Replace the values between curly braces by your configuration ans remove all curly braces
    - `{secret}` -> a random string
    - `{user}` -> your database user
    - `{password}` -> your database password
    - `{host}` -> your database address (ex: 127.0.0.1 or localhost)
    - `{port}` -> your database port (5432 by default for postgres)
    - `{dbname}` -> your database name
    - `{version}` -> your postgres server version (ex: 10.3)    
3. `composer install -o`
4. `php bin/console d:s:u -f`

## Licensing

Koillection is an Open Source software, released under the GNU General Public License v3.0. 
