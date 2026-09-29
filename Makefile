DC = docker compose
WEB = $(DC) exec web

DB_DUMP_DIR := ./www/backups
DB_DUMP_FILE := $(DB_DUMP_DIR)/dump-$(shell date +%Y-%m-%dT%H-%M).sql.gz

.PHONY: up upb build down restart bash cr cst cex cim db-dump composer composer-show

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

db-dump:
	mkdir -p $(DB_DUMP_DIR)
	docker compose exec web ./vendor/bin/drush sql-dump | gzip -9 > $(DB_DUMP_FILE)

composer:
	$(DC) exec web composer
composer-show:
	$(DC) exec web composer show
