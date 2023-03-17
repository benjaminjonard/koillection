# Cheat sheets

## Symfony
###### Update javascript translations
- `php bin/console app:translations:dump`


## Assets
All in assets directory
###### Reload assets
- `yarn build`
###### Generate stats
- `yarn run --silent build --json > stats.json`
- `yarn webpack-bundle-analyzer stats.json ../public/build`


## Tools
###### PhpUnit
Init test database if not already created :
- `php bin/console doctrine:database:create --env=test`
- `php bin/console doctrine:migrations:migrate --env=test`

Create API keys
- `php bin/console lexik:jwt:generate-keypair`

Then
- `composer test:phpunit` 
- or `composer test:paratest` for parallel tests (faster)
- or `composer test:coverage` for test coverage

###### PHP CS fixer
- `./vendor/bin/php-cs-fixer fix`

###### Rector
- `./vendor/bin/rector --dry-run`