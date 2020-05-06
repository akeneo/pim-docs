UID = $(shell id -u)
GID = $(shell id -g)
DOCKER_IMAGE = pim-docs
DOCKER_RUN = docker run -it --rm -v $(PWD):/home/akeneo/pim-docs/ -u $(UID):$(GID) -v ~/.composer:/.composer

.DEFAULT_GOAL := build
.PHONY: build, deploy, docker-build

clean:
	rm -rf ee_dev
	rm -rf pim-docs-build && mkdir pim-docs-build
	rm -rf pim-docs-lint && mkdir pim-docs-lint
	rm -rf design_pim/styleguide/vendor

build: clean check-uses lint html styleguide
	@echo "\nYou are now ready to check the documentation locally in the directory \"pim-docs-build/\" and to deploy it with \"DEPLOY_HOSTNAME=foo.com DEPLOY_PORT=1985 VERSION=bar make deploy\"."

deploy: build
	$(DOCKER_RUN) -v $${SSH_AUTH_SOCK}:/ssh-auth.sock:ro -e SSH_AUTH_SOCK=/ssh-auth.sock $(DOCKER_IMAGE) rsync -e "ssh -q -p $${DEPLOY_PORT} -o StrictHostKeyChecking=no" -qarz --delete /home/akeneo/pim-docs/pim-docs-build/ akeneo@$${DEPLOY_HOSTNAME}:/var/www/${VERSION}

lint: docker-build
	$(DOCKER_RUN) $(DOCKER_IMAGE) sphinx-build -nWT -b linkcheck /home/akeneo/pim-docs/ /home/akeneo/pim-docs/pim-docs-lint

html: docker-build
	$(DOCKER_RUN) $(DOCKER_IMAGE) sphinx-build -b html /home/akeneo/pim-docs/ /home/akeneo/pim-docs/pim-docs-build

docker-build:
	docker build . --tag $(DOCKER_IMAGE)

check-uses: docker-build
	${DOCKER_RUN} -e COMPOSER_AUTH ${DOCKER_IMAGE} /home/akeneo/pim-docs/scripts/test_php_uses.sh

styleguide: docker-build
	$(DOCKER_RUN) -w /home/akeneo/pim-docs/design_pim/styleguide $(DOCKER_IMAGE) cp styleguide.js /home/akeneo/pim-docs/pim-docs-build/design_pim/styleguide/
	$(DOCKER_RUN) -w /home/akeneo/pim-docs/design_pim/styleguide $(DOCKER_IMAGE) cp styleguide.css /home/akeneo/pim-docs/pim-docs-build/design_pim/styleguide/
	$(DOCKER_RUN) -w /home/akeneo/pim-docs/design_pim/styleguide $(DOCKER_IMAGE) ./prepare_static_files.sh
	$(DOCKER_RUN) -w /home/akeneo/pim-docs/design_pim/styleguide -e COMPOSER_AUTH $(DOCKER_IMAGE) php /usr/local/bin/composer.phar install --no-plugins
	$(DOCKER_RUN) -w /home/akeneo/pim-docs/design_pim/styleguide $(DOCKER_IMAGE) bash -c "php index.php > /home/akeneo/pim-docs/pim-docs-build/design_pim/styleguide/index.html"
