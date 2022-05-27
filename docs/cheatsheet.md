# Cheat sheets

## Symfony
###### Update javascript translations
- `php bin/console bazinga:js-translation:dump assets/js --format=js`


## Assets
All in assets directory
###### Reload assets
- `yarn build`
###### Generate stats
- `yarn run --silent build --json > stats.json`
- `yarn webpack-bundle-analyzer stats.json ../public/build`


## Tools
###### PhpUnit
Init test database if not alreasy created :
- `php bin/console doctrine:database:create --env=test`
- `php bin/console doctrine:migrations:migrate --env=test`

Then
- `php -d memory_limit=512M ./bin/phpunit --stderr`

###### PHP CS fixer
- `./vendor/bin/php-cs-fixer fix`

###### Psalm
- `./vendor/bin/psalm`
- `./vendor/bin/psalm --alter --issues=MissingReturnType,MissingParamType --dry-run`