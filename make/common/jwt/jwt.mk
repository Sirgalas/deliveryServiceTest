jwt-create:
	docker-compose run --rm php-cli /bin/ash -c 'mkdir -p config/jwt && \
	openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:${APP_SECRET} && \
	openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:${APP_SECRET} && \
	chmod 0755 -R config/jwt'

jwt-remove:
	docker-compose run --rm php-cli sh -c 'rm -rf config/jwt'