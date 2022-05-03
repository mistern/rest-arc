.DEFAULT_GOAL := help
ENV = dev

help: ## Show help message.
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m\033[0m\n"} /^[$$()% 0-9a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

.PHONY: migrations
migrations: ## Run DB migrations.
	bin/console -e $(ENV) cache:clear
	bin/console -e $(ENV) doctrine:database:drop --if-exists --force
	bin/console -e $(ENV) doctrine:database:create --if-not-exists
	bin/console -e $(ENV) doctrine:migrations:migrate --no-interaction

.PHONE: it
it: sa tests ## Run all checks and tests.

.PHONY: sa
sa: vendor ## Run static analysis.
	vendor/bin/psalm --no-progress --no-cache

.PHONY: tests ## Run tests.
tests: ENV = test
tests: vendor migrations
	vendor/bin/phpunit

vendor: composer.json composer.lock ## Update vendors.
	composer validate
	composer install
	@touch $@
