install:
	docker compose up -d
	sleep 5
	php bin/console doctrine:database:create --if-not-exists
	php bin/console doctrine:schema:update --force
	php bin/console doctrine:fixtures:load --no-interaction
	sleep 5
	php bin/console doctrine:database:create --if-not-exists --env=test
	php bin/console doctrine:schema:update --force --env=test
	php bin/console doctrine:fixtures:load --no-interaction --env=test

down:
	docker compose down