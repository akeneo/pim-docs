UID = $(shell id -u)
GID = $(shell id -g)
DOCKER_IMAGE = pim-docs
DOCKER_RUN = docker run -it --rm -u $(UID):$(GID)

.DEFAULT_GOAL := build
.PHONY: build, deploy, docker-build

build:
	@echo "\nBuilding Docker image..."
	make docker-build
	@echo "\nChecking documentation errors..."
	make lint
	@echo "\nBuilding documentation..."
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs/data $(DOCKER_IMAGE) sphinx-build -b html /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-build
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs/data $(DOCKER_IMAGE) cp -L -r /home/akeneo/pim-docs/pim-community-dev-master/public /home/akeneo/pim-docs/data/pim-docs-build/
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs/data $(DOCKER_IMAGE) cp -r /home/akeneo/pim-docs/data/design_pim/styleguide /home/akeneo/pim-docs/data/pim-docs-build/design_pim/
	@echo "\nYou are now ready to check the documentation locally in the directory \"pim-docs-build/\" and to deploy it with \"HOSTNAME=foo.com PORT=1985 VERSION=bar make deploy\"."

deploy:
	docker run -it --rm -v $${SSH_AUTH_SOCK}:/ssh-auth.sock:ro -e SSH_AUTH_SOCK=/ssh-auth.sock -v $(PWD):/home/akeneo/pim-docs/data $(DOCKER_IMAGE) rsync -e "ssh -p ${PORT} -o StrictHostKeyChecking=no" -arvz /home/akeneo/pim-docs/data/pim-docs-build/ akeneo@${HOSTNAME}:/var/www/${VERSION}

lint:
	rm -rf pim-docs-build && mkdir pim-docs-build
	rm -rf pim-docs-lint && mkdir pim-docs-lint
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs/data $(DOCKER_IMAGE) sphinx-build -nWT -b linkcheck /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-lint

docker-build:
	docker build . --tag $(DOCKER_IMAGE)
