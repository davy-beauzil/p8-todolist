ToDoList
========

Base du projet #8 : Améliorez un projet existant

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1

## Installation

```
docker compose up -d
php bin/console doctrine:schema:create --env=dev
php bin/console doctrine:schema:create --env=test
php bin/console doctrine:fixtures:load --env=dev
php bin/console doctrine:fixtures:load --env=test
```
