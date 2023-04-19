install:
	docker compose up -d
	yarn install
	yarn build
	sleep 15
	php bin/console doctrine:database:create --if-not-exists
	php bin/console doctrine:schema:update --force
	php bin/console h:f:l --no-bundles --no-interaction
	sleep 15
	php bin/console doctrine:database:create --if-not-exists --env=test
	php bin/console doctrine:schema:update --force --env=test
	php bin/console h:f:l --no-bundles --no-interaction --env=test
	ls ./public

down:
	docker compose down

phpstan:
	vendor/bin/phpstan analyse

rector:
	vendor/bin/rector process --dry-run

ecs:
	vendor/bin/ecs check

check:
	make phpstan
	vendor/bin/rector process
	vendor/bin/ecs --fix

tests:
	vendor/bin/phpunit