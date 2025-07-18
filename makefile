#!/usr/bin/make

.DEFAULT_GOAL := help

COMPOSE_RUNNER ?= "docker-compose"

setup: ## Setup the project
	@make prune
	@make install
	@make up

##@ Bash controls

bash: ## Start nginx bash
	@make up
	@$(COMPOSE_RUNNER) exec morph bash

up: ## Start the project
	@$(COMPOSE_RUNNER) up -d

down: ## Stop the project
	@$(COMPOSE_RUNNER) down --remove-orphans

prune: ## Prune the project
	@$(COMPOSE_RUNNER) down --remove-orphans --volumes


##@ Composer

install: ## Composer install dependencies
	@$(COMPOSE_RUNNER) run --rm --entrypoint composer morph install

dump: ## Run the composer dump
	@$(COMPOSE_RUNNER) run --rm --entrypoint composer morph dump-autoload


##@ Code analysis

lint: ## Perform code style lint
	@$(COMPOSE_RUNNER) run --rm --entrypoint composer morph lint

lint-phpcs: ## Perform code style list using phpcs
	@$(COMPOSE_RUNNER) run --rm --entrypoint composer morph lint:phpcs

lint-phpstan: ## Perform code style list using phpstan
	@$(COMPOSE_RUNNER) run --rm --entrypoint composer morph lint:phpstan

lint-phpmd: ## Perform code style list using phpmd
	@$(COMPOSE_RUNNER) run --rm --entrypoint composer morph lint:phpmd

lint-rector: ## Perform code style list using rector
	@$(COMPOSE_RUNNER) run --rm --entrypoint composer morph lint:rector

lint-psalm: ## Perform code style list using psalm
	@$(COMPOSE_RUNNER) run --rm --entrypoint composer morph lint:psalm

fix: ## Perform code style fix
	@$(COMPOSE_RUNNER) run --rm --entrypoint composer morph fix

##@ Tests

test: ## Execute the tests
	@$(COMPOSE_RUNNER) run --rm --entrypoint composer morph test -- --coverage-html tests/.phpunit/html


##@ CI

ci: ## Execute all analysis as CI does
	@$(COMPOSE_RUNNER) run --rm --entrypoint composer morph ci


## Quality

sonar: ## Run the sonar analysis
	@$(COMPOSE_RUNNER) run --rm --entrypoint "/bin/sonar-scanner" morph -Dsonar.host.url=https://sonarcloud.io -X


##@ Docs

help: ## Print the makefile help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
