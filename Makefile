UID = $(shell id -u)
GID = $(shell id -g)
DOCKER_IMAGE = pim-docs
DOCKER_RUN = docker run -it --rm -u $(UID):$(GID)

.DEFAULT_GOAL := build
.PHONY: build, deploy, docker-build

clean:
	rm -rf pim-docs-build && mkdir pim-docs-build
	rm -rf pim-docs-lint && mkdir pim-docs-lint
	rm -rf design_pim/styleguide/vendor

build: clean lint html styleguide
	@echo "\nYou are now ready to check the documentation locally in the directory \"pim-docs-build/\" and to deploy it with \"DEPLOY_HOSTNAME=foo.com DEPLOY_PORT=1985 VERSION=bar make deploy\"."

deploy: build
	docker run -it --rm -v $${SSH_AUTH_SOCK}:/ssh-auth.sock:ro -e SSH_AUTH_SOCK=/ssh-auth.sock -v $(PWD):/home/akeneo/pim-docs/ $(DOCKER_IMAGE) rsync -e "ssh -q -p $${DEPLOY_PORT} -o StrictHostKeyChecking=no" -qarz --delete /home/akeneo/pim-docs/pim-docs-build/ akeneo@$${DEPLOY_HOSTNAME}:/var/www/${VERSION}

lint: docker-build
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs/ $(DOCKER_IMAGE) sphinx-build -nWT -b linkcheck /home/akeneo/pim-docs/ /home/akeneo/pim-docs/pim-docs-lint

html:
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs/ $(DOCKER_IMAGE) sphinx-build -b html /home/akeneo/pim-docs/ /home/akeneo/pim-docs/pim-docs-build

docker-build:
	docker build . --tag $(DOCKER_IMAGE)

styleguide:
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs $(DOCKER_IMAGE) cp pim-docs/design_pim/styleguide/styleguide.js pim-docs/pim-docs-build/design_pim/styleguide/
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs $(DOCKER_IMAGE) cp pim-docs/design_pim/styleguide/styleguide.css pim-docs/pim-docs-build/design_pim/styleguide/
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs $(DOCKER_IMAGE) pim-docs/design_pim/styleguide/prepare_static_files.sh
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs -w /home/akeneo/pim-docs/design_pim/styleguide $(DOCKER_IMAGE) php /usr/local/bin/composer.phar install
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs -w /home/akeneo/pim-docs/design_pim/styleguide $(DOCKER_IMAGE) bash -c "php index.php > /home/akeneo/pim-docs/pim-docs-build/design_pim/styleguide/index.html"
