build:
	composer install

.PHONY: test
test:
	./vendor/bin/phpunit

test_publish:
	./vendor/bin/phpunit --log-junit test_results.xml
