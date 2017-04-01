build:
	composer install

.PHONY: test
test:
	./vendor/bin/phpunit

test_publish:
	./vendor/bin/phpunit --log-junit test_results.xml

# requires PERFORM_GIT_SERVER to be set
push_packages:
	./bin/push-parent.sh
	./bin/subsplit.sh BaseBundle base-bundle

clean_bundles:
	rm -rf src/*/vendor
