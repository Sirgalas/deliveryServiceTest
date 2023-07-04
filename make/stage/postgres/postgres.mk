postgres-dump-create:
	docker-compose exec -u postgres postgres pg_dump -Fc ${POSTGRES_DB} > dump.sql

postgres-dump-restore:
	docker-compose exec -T postgres psql -U ${POSTGRES_USER} -d ${POSTGRES_DB} < dump.sql
