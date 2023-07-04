doctrine-schema-update:
	docker-compose run --rm php-cli bin/console app:schema:update -f

doctrine-schema-validate:
	docker-compose run --rm php-cli bin/console doctrine:schema:validate
