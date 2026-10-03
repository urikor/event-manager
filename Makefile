DC = docker compose
WEB = $(DC) exec web
WEB_USER = $(DC) exec --user $(shell id -u):$(shell id -g) web

DB_DUMP_DIR := ./www/backups
DB_DUMP_FILE := $(DB_DUMP_DIR)/dump-$(shell date +%Y-%m-%dT%H-%M).sql.gz

.PHONY: up upb build down restart bash drush db-dump db-drop composer

up:
	$(DC) up -d

upb:
	$(DC) up -d --build

build:
	$(DC) build

down:
	$(DC) down

restart:
	$(DC) restart

bash:
	$(WEB) bash

drush:
	$(WEB_USER) ./vendor/bin/drush $(filter-out $@,$(MAKECMDGOALS))

db-dump:
	mkdir -p $(DB_DUMP_DIR)
	$(WEB_USER) ./vendor/bin/drush sql-dump | gzip -9 > $(DB_DUMP_FILE)
db-drop:
	$(WEB_USER) ./vendor/bin/drush sql-drop

composer:
	$(WEB_USER) composer $(filter-out $@,$(MAKECMDGOALS))
