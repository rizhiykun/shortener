APP=symfony

psalm:
	docker exec $(APP) php ./vendor/bin/psalm --no-cache

run_unit_test:
	docker exec $(APP) php vendor/bin/phpunit --testdox

unit_test_coverage:
	docker exec $(APP) php vendor/bin/phpunit --coverage-html var/coverage

ecs_fix:
	docker exec $(APP) php ./vendor/bin/ecs --fix

ecs:
	docker exec $(APP) php ./vendor/bin/ecs

build:
	@docker exec $(APP) git config --global --add safe.directory /var/www
	@docker exec $(APP) composer install
	@docker exec $(APP) bin/console doctrine:migrations:migrate --no-interaction
