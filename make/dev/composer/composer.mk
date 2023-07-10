composer-install:
	docker-compose run --rm php-cli composer install

composer-cache-clear:
	docker-compose run --rm php-cli composer clear-cache --no-interaction --no-cache

composer-update:
	docker-compose run --rm php-cli composer update

composer-require:
	docker-compose run --rm php-cli composer require $(filter-out $@, $(MAKECMDGOALS))

composer-require-dev:
	docker-compose run --rm php-cli composer require $(filter-out $@, $(MAKECMDGOALS)) --dev

composer-remove:
	docker-compose run --rm php-cli composer remove $(filter-out $@, $(MAKECMDGOALS))

check-requirements:
	docker-compose run --rm php-cli composer check-requirements
