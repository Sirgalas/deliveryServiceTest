doctrine-fixtures-load:
	docker-compose run --rm php-cli bin/console doctrine:fixtures:load --no-interaction

doctrine-fixtures-load-append:
	docker-compose run --rm php-cli bin/console doctrine:fixtures:load --append --no-interaction
