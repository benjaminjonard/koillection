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


## Tests
###### PhpUnit
- `php -d memory_limit=512M ./bin/phpunit --stderr`

