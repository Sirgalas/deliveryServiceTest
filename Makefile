include ./.env
export $(shell sed 's/=.*//' ./.env)

-include ./make/common/**/*.mk
-include ./make/${APP_ENV}/**/*.mk
-include ./make/${APP_ENV}/*.mk

install: pre-commit

pre-commit:
	cp -f pre-commit .git/hooks/pre-commit && chmod 777 .git/hooks/pre-commit
