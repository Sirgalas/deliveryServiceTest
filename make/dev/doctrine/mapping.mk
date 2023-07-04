doctrine-mapping-import:
	docker-compose run --rm php-cli bin/console doctrine:mapping:import "App\DomainModel" annotation --path=${APP_DIR}/var/tmp/DomainModel
