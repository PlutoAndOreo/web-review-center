# Variables
DOCKER_COMPOSE = docker compose
PROJECT_NAME = web-review-center
PHP_CONTAINER = php
NGINX_CONTAINER = nginx
MYSQL_CONTAINER = mysql
NODE_CONTAINER = node

# Default target
.PHONY: help
help:
	@echo "Usage:"
	@echo "  make build         Build all services"
	@echo "  make up            Start all services in detached mode"
	@echo "  make down          Stop all services"
	@echo "  make restart       Restart all services"
	@echo "  make logs          View logs for all services"
	@echo "  make clean         Remove all containers, networks, and volumes"
	@echo "  make bash-nginx    Access the nginx container bash"
	@echo "  make bash-php      Access the php container bash"
	@echo "  make bash-mysql    Access the mysql container bash"
	@echo "  make bash-node     Access the node container shell"
	@echo "  make artisan c=xxx Run artisan command (ex: make artisan c=migrate)"
	@echo "  make composer c=xx Run composer command (ex: make composer c=install)"
	@echo "  make npm c=xxx     Run npm command in node (ex: make npm c=install)"
	@echo "  make npm-dev       Run npm run dev (Vite hot reload)"
	@echo "  make npm-build     Run npm run build"
	@echo "  make cache-clear   Clear Laravel cache/config/routes/views"

# Build all services
.PHONY: build
build:
	$(DOCKER_COMPOSE) build

# Start all services in detached mode
.PHONY: up
up:
	$(DOCKER_COMPOSE) up -d

# Stop all services
.PHONY: down
down:
	$(DOCKER_COMPOSE) down

# Restart all services
.PHONY: restart
restart: down up

# View logs for all services
.PHONY: logs
logs:
	$(DOCKER_COMPOSE) logs -f

# Clean up containers, networks, and volumes
.PHONY: clean
clean:
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

# Access containers
.PHONY: bash-nginx
bash-nginx:
	$(DOCKER_COMPOSE) exec $(NGINX_CONTAINER) bash

.PHONY: bash-php
bash-php:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) bash

.PHONY: bash-mysql
bash-mysql:
	$(DOCKER_COMPOSE) exec $(MYSQL_CONTAINER) bash

.PHONY: bash-node
bash-node:
	$(DOCKER_COMPOSE) exec $(NODE_CONTAINER) sh

# Laravel Helpers
.PHONY: artisan
artisan:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php artisan $(c)

.PHONY: composer
composer:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) composer $(c)

# Node / Vite helpers
.PHONY: npm
npm:
	$(DOCKER_COMPOSE) exec $(NODE_CONTAINER) npm $(c)

.PHONY: npm-dev
npm-dev:
	$(DOCKER_COMPOSE) exec $(NODE_CONTAINER) npm run dev

.PHONY: npm-build
npm-build:
	$(DOCKER_COMPOSE) exec $(NODE_CONTAINER) npm run build

# Laravel Cache
.PHONY: cache-clear
cache-clear:
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php artisan cache:clear
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php artisan config:clear
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php artisan route:clear
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php artisan view:clear
