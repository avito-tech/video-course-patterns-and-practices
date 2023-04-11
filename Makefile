TAG_DEV=w7493/test_framework

help:
	@echo "deploy - deploy project. Use 127.0.0.1:8080 in your browser"
	@echo "delete - remove project"
	@echo "build - build docker image"
	@echo "composer.install - install vendors using composer.lock file"
	@echo "composer.require package='vendor/something ^1.0' - add package to composer.json"
	@echo "composer.remove package='vendor/something ^1.0' - remove package from composer.json"
	@echo "composer.update - update vendor dependencies"
	@echo "test - run unit tests"
	@echo "docker.attach - attach your terminal into a docker shell"

.PHONY: deploylocal
deploylocal:
	php -S 0.0.0.0:8080 -t web

.PHONY: deploy
deploy: build
	docker-compose up -d

.PHONY: delete
delete:
	docker-compose down

.PHONY: build
build:
	docker build -t ${TAG_DEV}:latest .

.PHONY: composer.install
composer.install:
	docker run --rm -i -v $(shell pwd):/app -t ${TAG_DEV}:latest composer install --prefer-dist

.PHONY: composer.require
composer.require:
	docker run --rm -i -v $(shell pwd):/app -t ${TAG_DEV}:latest composer require ${package}

.PHONY: composer.remove
composer.remove:
	docker run --rm -i -v $(shell pwd):/app -t ${TAG_DEV}:latest composer remove ${package}

.PHONY: composer.update
composer.update:
	docker run --rm -i -v $(shell pwd):/app -t ${TAG_DEV}:latest composer update

.PHONY: test
test:
	docker run --rm -i -v $(shell pwd):/app -t ${TAG_DEV}:latest bin/phpunit test

.PHONY: docker.attach
docker.attach:
	docker run --rm -i -v $(shell pwd):/app -ti ${TAG_DEV}:latest /bin/bash
