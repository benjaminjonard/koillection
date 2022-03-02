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
- `php -d memory_limit=512M ./bin/phpunit --stderr`

###### PHP CS fixer
- `./vendor/bin/php-cs-fixer fix src`
- `./vendor/bin/php-cs-fixer fix api`

- `./vendor/bin/php-cs-fixer fix src --allow-risky=yes --rules=native_function_invocation`
- `./vendor/bin/php-cs-fixer fix api --allow-risky=yes --rules=native_function_invocation`

###### Psalm
- `./vendor/bin/psalm`