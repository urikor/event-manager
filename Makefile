DC = docker compose
WEB = $(DC) exec web

DB_DUMP_DIR := ./www/backups
DB_DUMP_FILE := $(DB_DUMP_DIR)/dump-$(shell date +%Y-%m-%dT%H-%M).sql.gz

.PHONY: up upb build down restart bash cr cst cex cim updb status uli db-dump db-drop composer composer-show

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

cr:
	$(WEB) ./vendor/bin/drush cr
cst:
	$(WEB) ./vendor/bin/drush cst
cex:
	$(WEB) ./vendor/bin/drush cex
cim:
	$(WEB) ./vendor/bin/drush cim

updb:
	$(WEB) ./vendor/bin/drush updb
status:
	$(WEB) ./vendor/bin/drush status
uli:
	$(WEB) ./vendor/bin/drush uli
db-dump:
	mkdir -p $(DB_DUMP_DIR)
	$(WEB) ./vendor/bin/drush sql-dump | gzip -9 > $(DB_DUMP_FILE)
db-drop:
	$(WEB) ./vendor/bin/drush sql-drop

composer:
	$(DC) exec web composer
composer-show:
	$(DC) exec web composer show
