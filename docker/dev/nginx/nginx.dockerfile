ARG nginx_image
FROM $nginx_image
RUN --mount=type=cache,target=/var/cache/apk set -ex \
    && apk add nginx-module-image-filter
