doctrine-database-drop:
	docker-compose run --rm php-cli bin/console doctrine:database:drop -f

doctrine-database-create:
	docker-compose run --rm php-cli bin/console doctrine:database:create
