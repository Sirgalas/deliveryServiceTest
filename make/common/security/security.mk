security-hash-password:
	docker-compose run --rm php-cli bin/console security:hash-password --empty-salt $(filter-out $@, $(MAKECMDGOALS))
