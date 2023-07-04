build-stage:
	docker --log-level=debug build --pull --file=docker/stage/nginx/nginx.dockerfile --tag=${REGISTRY}/platform-nginx:${IMAGE_TAG} --build-arg nginx_image=${NGINX_IMAGE} .
	docker --log-level=debug build --pull --file=docker/stage/postgres/postgres.dockerfile --tag=${REGISTRY}/platform-postgres:${IMAGE_TAG} .
	docker --log-level=debug build --pull --file=docker/stage/php-fpm/php-fpm.dockerfile --tag=${REGISTRY}/platform-php-fpm:${IMAGE_TAG} --build-arg php_fpm_image=${PHP_FPM_IMAGE} .
	docker --log-level=debug build --pull --file=docker/stage/consumer/consumer.dockerfile --tag=${REGISTRY}/platform-consumer:${IMAGE_TAG} --build-arg php_cli_image=${PHP_CLI_IMAGE} .
	docker --log-level=debug build --pull --file=docker/stage/cron/cron.dockerfile --tag=${REGISTRY}/platform-cron:${IMAGE_TAG} --build-arg php_fpm_image=${PHP_FPM_IMAGE} .
	docker --log-level=debug build --pull --file=docker/stage/memcached/memcached.dockerfile --tag=${REGISTRY}/platform-memcached:${IMAGE_TAG} .
	docker --log-level=debug build --pull --file=docker/stage/php-cli/php-cli.dockerfile --tag=${REGISTRY}/platform-api-php-cli:${IMAGE_TAG} --build-arg php_cli_image=${PHP_CLI_IMAGE} .
	docker --log-level=debug build --pull --file=docker/stage/centrifugo/centrifugo.dockerfile --tag=${REGISTRY}/platform-centrifugo:${IMAGE_TAG} .
	docker --log-level=debug build --pull --file=docker/stage/rabbitmq/rabbit.dockerfile --tag=${REGISTRY}/platform-rabbit:${IMAGE_TAG} .
