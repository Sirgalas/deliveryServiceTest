composer-install:
	docker-compose run --rm php-cli composer install --no-interaction --no-progress --no-dev --no-cache --profile --no-scripts
	make composer-clear-cache
	make composer-dump-autoload

composer-clear-cache:
	docker-compose run --rm php-cli composer clear-cache --no-interaction --no-cache

composer-dump-autoload:
	docker-compose run --rm php-cli composer dump-autoload --no-dev --no-cache --profile --optimize
