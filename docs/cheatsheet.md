# Cheat sheets

#### Symfony
###### Update javascript translations
- `php bin/console bazinga:js-translation:dump assets/js --format=js`

###### Reload assets
- `cd ./assets/ && yarn encore production`

#### Docker
###### Push a new image to github repo
- `docker build . --no-cache`
- `docker tag {tag_returned_by_previous_command} docker.pkg.github.com/koillection/koillection/{image}:{tag}`
- `docker push docker.pkg.github.com/koillection/koillection/{image}:{tag}`


