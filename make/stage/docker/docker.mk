docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull --include-deps

docker-build:
	docker-compose build

docker-ps:
	docker-compose ps

docker-logs:
	docker-compose logs $(filter-out $@, $(MAKECMDGOALS))

docker-sh:
	docker-compose exec $(filter-out $@, $(MAKECMDGOALS)) sh

docker-init: docker-down-clear docker-pull docker-build docker-up
