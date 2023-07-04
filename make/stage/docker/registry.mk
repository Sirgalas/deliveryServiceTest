push: push-api

push-api:
	docker push ${REGISTRY}/platform-nginx:${IMAGE_TAG}
	docker push ${REGISTRY}/platform-postgres:${IMAGE_TAG}
	docker push ${REGISTRY}/platform-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/platform-consumer:${IMAGE_TAG}
	docker push ${REGISTRY}/platform-cron:${IMAGE_TAG}
	docker push ${REGISTRY}/platform-memcached:${IMAGE_TAG}
	docker push ${REGISTRY}/platform-api-php-cli:${IMAGE_TAG}
	docker push ${REGISTRY}/platform-centrifugo:${IMAGE_TAG}
	docker push ${REGISTRY}/platform-rabbit:${IMAGE_TAG}
