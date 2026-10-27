# Variables
DOCKER_COMPOSE       = docker compose
PROJECT_NAME         = web-review-center
PHP_CONTAINER        = php
NGINX_CONTAINER      = nginx
MYSQL_CONTAINER      = mysql
NODE_CONTAINER       = node
DOCKER_COMPOSE_PROD  = docker compose -f docker-compose-prod.yml

# Default target
.PHONY: help
help:
	@echo "Usage:"
	@echo "  make build             Build all Docker images"
	@echo "  make up                Start all services"
	@echo "  make down              Stop all services"
	@echo "  make restart           Restart all services"
	@echo "  make logs              View service logs"
	@echo "  make clean             Remove containers, networks, and volumes"
	@echo "  make bash-nginx        Access nginx container"
	@echo "  make bash-php          Access PHP container"
	@echo "  make bash-mysql        Access MySQL container"
	@echo "  make bash-node         Access Node container"
	@echo "  make artisan c=xxx     Run Laravel Artisan command"
	@echo "  make composer c=xxx    Run Composer command"
	@echo "  make npm c=xxx         Run npm command"
	@echo "  make npm-dev           Run Vite development server"
	@echo "  make npm-build         Build frontend assets"
	@echo "  make cache-clear       Clear Laravel caches"
	@echo "  make build-prod        Build and install production dependencies"

# Development
.PHONY: build
build:
	$(DOCKER_COMPOSE) build

.PHONY: up
up:
	$(DOCKER_COMPOSE) up -d

.PHONY: down
down:
	$(DOCKER_COMPOSE) down

.PHONY: restart
restart: down up

.PHONY: logs
logs:
	$(DOCKER_COMPOSE) logs -f

.PHONY: clean
clean:
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

# Container access
.PHONY: bash-nginx
bash-nginx:
	$(DOCKER_COMPOSE) exec $(NGINX_CONTAINER) sh

.PHONY: bash-php
bash-php:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) sh

.PHONY: bash-mysql
bash-mysql:
	$(DOCKER_COMPOSE) exec $(MYSQL_CONTAINER) sh

.PHONY: bash-node
bash-node:
	$(DOCKER_COMPOSE) exec $(NODE_CONTAINER) sh

# Laravel / Composer
.PHONY: artisan
artisan:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php artisan $(c)

.PHONY: composer
composer:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) composer $(c)

# Node / Vite
.PHONY: npm
npm:
	$(DOCKER_COMPOSE) exec $(NODE_CONTAINER) npm $(c)

.PHONY: npm-dev
npm-dev:
	$(DOCKER_COMPOSE) exec $(NODE_CONTAINER) npm run dev

.PHONY: npm-build
npm-build:
	$(DOCKER_COMPOSE) exec $(NODE_CONTAINER) npm run build

# Laravel cache
.PHONY: cache-clear
cache-clear:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php artisan cache:clear
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php artisan config:clear
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php artisan route:clear
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php artisan view:clear

# Production
.PHONY: build-prod
build-prod:
	$(DOCKER_COMPOSE_PROD) build
	$(DOCKER_COMPOSE_PROD) up -d
	$(DOCKER_COMPOSE_PROD) exec $(PHP_CONTAINER) composer install --no-dev --optimize-autoloader --no-interaction
	$(DOCKER_COMPOSE_PROD) exec $(NODE_CONTAINER) npm run build

.PHONY: up-prod
up-prod:
	$(DOCKER_COMPOSE_PROD) up -d

.PHONY: down-prod
down-prod:
	$(DOCKER_COMPOSE_PROD) down

.PHONY: logs-prod
logs-prod:
	$(DOCKER_COMPOSE_PROD) logs -f
