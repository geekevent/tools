.PHONY: help

help:
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
	| sed -n 's/^\(.*\): \(.*\)##\(.*\)/\1\3/p'

cc-test: ## clear cache for test env
	bin/console ca:cl --env=test

db-test: cc-test ## create test database
	bin/console do:da:cr --env=test --if-not-exists
	bin/console do:sc:up -f --env=test

phpunit: db-test ## run unit test
	vendor/bin/phpunit

cc: ## clear cache for test env
	bin/console ca:cl

db: cc ## create test database
	bin/console do:da:cr --if-not-exists
	bin/console do:sc:up -f

