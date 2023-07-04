doctrine-migration-migrate:
	docker-compose run --rm php-cli bin/console doctrine:migration:migrate --no-interaction

doctrine-migration-up:
	docker-compose run --rm php-cli bin/console doctrine:migration:exec --up --no-interaction $(filter-out $@, $(MAKECMDGOALS))

doctrine-migration-down:
	docker-compose run --rm php-cli bin/console doctrine:migration:exec --down --no-interaction $(filter-out $@, $(MAKECMDGOALS))
