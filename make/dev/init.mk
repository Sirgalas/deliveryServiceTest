init: docker-init composer-install jwt-create doctrine-migration-migrate doctrine-fixtures-load-append consumer-start
die: docker-down-clear
