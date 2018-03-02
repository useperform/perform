build:
	composer install

.PHONY: test
test:
	SYMFONY_DEPRECATIONS_HELPER="weak_vendors" ./vendor/bin/phpunit -v

test_publish:
	SYMFONY_DEPRECATIONS_HELPER="weak_vendors" ./vendor/bin/phpunit -v --log-junit test_results.xml

docker_image:
	./bin/build-docker-image.sh $(php-version)

docker_test:
	docker run -i --rm -v `pwd`:/opt/perform perform:$(php-version) make test_publish

clean:
	rm -f Dockerfile
	rm -f composer.lock
	rm -f test_results.xml

clean_all: clean clean_bundles
	rm -rf vendor

clean_bundles:
	rm -rf src/*/vendor

.PHONY: docs
docs:
	./bin/gendocs.php
	(cd docs/_themes/perform && yarn install && npm run build)
