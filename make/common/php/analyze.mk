php-analyze: php-lint php-cs-check php-phpstan php-psalm

php-phpstan:
	docker-compose run --rm php-cli composer phpstan

php-psalm:
	docker-compose run --rm php-cli composer psalm

php-lint:
	docker-compose run --rm php-cli composer lint

php-cs-check:
	docker-compose run --rm php-cli composer cs-check

php-rector:
	docker-compose run --rm php-cli composer rector

php-console:
	docker-compose run --rm php-cli bin/console $(filter-out $@, $(MAKECMDGOALS))
