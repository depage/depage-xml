
test: vendor/bin/phpunit Tests/*.php
	vendor/bin/phpunit Tests/

vendor/bin/phpunit: composer.json
	composer install

clean:
	rm -rf vendor/
