DC = docker compose
WEB = $(DC) exec web

.PHONY: up upb build down restart bash cr cst cex cim composer composer-show

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

composer:
	$(DC) exec web composer
composer-show:
	$(DC) exec web composer show
