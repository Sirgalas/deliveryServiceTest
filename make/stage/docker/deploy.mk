define CLEAR_BUILD_DIR
rm -rf $$(ls $$(ls | grep "site_" | sort -k1r | head -n 3 | awk '\''{ print "--ignore="$$1 }'\'' | tr "\n" " ") | grep "site_")
endef

define CLEAR_DOCKER_IMAGES
docker images -q --filter=reference="${REGISTRY}/platform-*:*${IMAGE_TAG}" > need && \
docker images -q | uniq > all && \
(docker rmi -f $$(sdiff need all | grep "[>]" | uniq | awk '\''{ print $$2 }'\'' | tr "\n" " ") || exit 0) && \
rm need all
endef

define CLEAR_DOCKER_NETWORKS
docker network prune -f
endef

define CLEAR_DOCKER_VOLUMES
docker system prune --volumes -f
endef

app-deploy-stage:
	ssh platform@${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh platform@${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'
	envsubst < docker-compose-stage.yml > docker-compose-stage-env.yml
	scp -o StrictHostKeyChecking=no -P ${PORT} ./docker-compose-stage-env.yml platform@${HOST}:site_${BUILD_NUMBER}/docker-compose.yml
	ssh platform@${HOST} -p ${PORT} 'docker login -u=${USER} -p=${PASSWORD} ${REGISTRY}'
	ssh platform@${HOST} -p ${PORT} 'docker stop $$(docker ps -q) || exit 0'
	ssh platform@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose pull -q'
	ssh platform@${HOST} -p ${PORT} 'rm -f site'
	ssh platform@${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'
	make app-docker-compose-up-stage
	make app-clear-stage
	make app-docker-system-df-stage

app-clear-stage:
	ssh platform@${HOST} -p ${PORT} '${CLEAR_DOCKER_IMAGES}'
	ssh platform@${HOST} -p ${PORT} '${CLEAR_DOCKER_NETWORKS}'
	ssh platform@${HOST} -p ${PORT} '${CLEAR_DOCKER_VOLUMES}'
	ssh platform@${HOST} -p ${PORT} '${CLEAR_BUILD_DIR}'

app-docker-compose-up-stage:
	ssh platform@${HOST} -p ${PORT} 'cd site && docker-compose up --build --remove-orphans -d'

app-docker-system-df-stage:
	ssh platform@${HOST} -p ${PORT} 'docker system df'

app-rollback-stage:
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'

app-deploy-stage-clean:
	rm -f docker-compose-stage-env.yml .env .env.stage.local
