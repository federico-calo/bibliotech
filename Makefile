SHELL := /bin/sh
DC := docker compose

.PHONY: init up down restart logs composer shell db-shell cache-shell phpunit phpstan rector lint npm yarn node-shell build watch

init:
	cp -n settings.php.dist settings.php || true
	cp -n env.example .env || true
	$(DC) up --build -d

up:
	$(DC) up -d

down:
	$(DC) down --remove-orphans

restart: down up

logs:
	$(DC) logs -f

composer-%:
	$(DC) exec app composer $*

phpunit:
	$(DC) exec app vendor/bin/phpunit --color

phpstan:
	$(DC) exec app vendor/bin/phpstan analyse web

rector:
	$(DC) exec app vendor/bin/rector process web --dry-run

lint:
	$(DC) exec app vendor/bin/phpcs web

shell:
	$(DC) exec app sh

db-shell:
	$(DC) exec database mysql -u$${MYSQL_USER:-lamp} -p$${MYSQL_PASSWORD:-lamp} $${MYSQL_DATABASE:-lamp}

cache-shell:
	$(DC) exec cache redis-cli

npm-%:
	$(DC) exec node npm $*

node-shell:
	$(DC) exec node sh

install-swagger:
	@echo "🚀 Installing Swagger UI..."
	$(DC) exec node npm install swagger-ui-dist
	mkdir -p web/assets/swagger
	cp -r node_modules/swagger-ui-dist/* web/assets/swagger/

