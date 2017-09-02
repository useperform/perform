build:
	composer install

.PHONY: test
test:
	./vendor/bin/phpunit -v

test_publish:
	./vendor/bin/phpunit -v --log-junit test_results.xml

# requires PERFORM_GIT_SERVER to be set
push_packages:
	./bin/push-parent.sh
	./bin/subsplit.sh BaseBundle base-bundle
	./bin/subsplit.sh ContactBundle contact-bundle
	./bin/subsplit.sh DevBundle dev-bundle
	./bin/subsplit.sh MediaBundle media-bundle
	./bin/subsplit.sh NotificationBundle notification-bundle
	./bin/subsplit.sh UserBundle user-bundle

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
	(cd docs; make html)
