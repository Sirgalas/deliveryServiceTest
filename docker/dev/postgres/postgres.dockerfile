ARG postgres_image
FROM $postgres_image

COPY ./dev/postgres/docker-entrypoint-initdb.d /docker-entrypoint-initdb.d/
