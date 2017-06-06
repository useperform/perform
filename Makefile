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
	./bin/subsplit.sh ContactBundle contact-bundle
	./bin/subsplit.sh DevBundle dev-bundle
	./bin/subsplit.sh MediaBundle media-bundle
	./bin/subsplit.sh NotificationBundle notification-bundle

clean_bundles:
	rm -rf src/*/vendor

.PHONY: docs
docs:
	./bin/gendocs.php
	(cd docs; make html)
