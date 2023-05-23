# How to contribute on this project ?

## Prerequisites
___
- PHP 8.1
- Composer
- Docker et Docker Compose

## Install the project
___

### Clone the project
```bash
git clone git@github.com:davy-beauzil/p8-todolist.git
cd p8-todolist
```

### Before initializing the project
You must be sure the Mysql Service is not running on your machine. If it is, you must stop it.
```bash
# On linux
sudo service mysql stop
```

### Initialize the projet
```bash
make install
```
`make install` will do the following:
- start docker containers
- install dependances
- create dev and test databases
- create fixtures on databases

### Run the project
```bash
symfony serve
# Open the project on localhost:8000
```

## Contribute
___
It is forbidden to push directly to the `master` branch. To contribute to the project, you must create a branch from `master` and make a pull request.
## Code quality
___
To contribute to the project, you must respect the following code quality rules:
- write tests for each new feature
- use code quality tools and make sure they pass:
    - Easy Coding Standard `vendor/bin/ecs --fix`
    - Phpstan `vendor/bin/phpstan analyze`
    - Rector `vendor/bin/rector process`
- make sure the tests pass with `vendor/bin/phpunit`

