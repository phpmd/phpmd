default: build

build: install test
.PHONY: build

install:
	composer install
.PHONY: install

update:
	composer update
.PHONY: update

update-min:
	composer update --prefer-stable --prefer-lowest
.PHONY: update-min

update-no-dev:
	composer update --prefer-stable --no-dev
.PHONY: update-no-dev

test: vendor phpunit
.PHONY: test

test-min: update-min phpunit
.PHONY: test-min

phpunit: tools/phpunit
	tools/phpunit
.PHONY: phpunit

phpunit-coverage: tools/phpunit
	phpdbg -qrr tools/phpunit
.PHONY: phpunit

package: tools/box
	@rm -rf build/phar && mkdir -p build/phar build/phar/bin

	cp -r src/main composer.json LICENSE build/phar

	cd build/phar && composer config platform.php 7.1 && composer update --no-dev -o -a

	tools/box compile

	@rm -rf build/phar
.PHONY: package

vendor: install

vendor/bin/phpunit: install

tools/phpunit: vendor/bin/phpunit
	ln -sf ../vendor/bin/phpunit tools/phpunit

tools/box:
	curl -Ls https://github.com/humbug/box/releases/download/3.7.0/box.phar -o tools/box && chmod +x tools/box
