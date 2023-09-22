UID = $(shell id -u)
GID = $(shell id -g)
DOCKER_IMAGE = pim-docs
DOCKER_RUN = docker run -it --rm -u $(UID):$(GID) -v /etc/passwd:/etc/passwd:ro -v $(PWD):/home/akeneo/pim-docs/data
DOCKER_RSYNC = $(DOCKER_RUN) -v $${SSH_AUTH_SOCK}:/ssh-auth.sock:ro -e SSH_AUTH_SOCK=/ssh-auth.sock $(DOCKER_IMAGE) rsync -e "ssh -q -p $${DEPLOY_PORT} -o StrictHostKeyChecking=no" -qarz --delete

.DEFAULT_GOAL := build
.PHONY: build, deploy, docker-build, update-versions

build: lint
	# Flags used here, not in `make html`:
	#  -n   Run in nit-picky mode. Currently, this generates warnings for all missing references.
	#  -W   Turn warnings into errors. This means that the build stops at the first warning and sphinx-build exits with exit status 1.
	#  -T   Displays the full stack trace if an unhandled exception occurs.
	#  -b   Linkcheck builder checks for broken links.
	$(DOCKER_RUN) $(DOCKER_IMAGE) sphinx-build -nWT -b html /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-build
	@echo "\nYou are now ready to check the documentation locally in the directory \"pim-docs-build/\" and to deploy it with \"DEPLOY_HOSTNAME=foo.com DEPLOY_PORT=1985 VERSION=bar make deploy\"."

deploy: build update-versions
	$(DOCKER_RUN) -v $${SSH_AUTH_SOCK}:/ssh-auth.sock:ro -e SSH_AUTH_SOCK=/ssh-auth.sock $(DOCKER_IMAGE) rsync -e "ssh -q -p $${DEPLOY_PORT} -o StrictHostKeyChecking=no" -qarz --delete /home/akeneo/pim-docs/data/pim-docs-build/ akeneo@$${DEPLOY_HOSTNAME}:/var/www/${VERSION}

lint: docker-build
	rm -rf pim-docs-build && mkdir pim-docs-build
	rm -rf pim-docs-lint && mkdir pim-docs-lint
	$(DOCKER_RUN) -v $(PWD):/home/akeneo/pim-docs/data $(DOCKER_IMAGE) sphinx-build -nWT -b linkcheck /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-lint

docker-build:
	docker build . --tag $(DOCKER_IMAGE)

update-versions:
	$(DOCKER_RSYNC) akeneo@$${DEPLOY_HOSTNAME}:/var/www/versions.json /home/akeneo/pim-docs/data
	$(DOCKER_RUN) -w /home/akeneo/pim-docs/data $(DOCKER_IMAGE) php scripts/update-doc-versions.php $(CIRCLE_BRANCH) versions.json
	$(DOCKER_RSYNC) /home/akeneo/pim-docs/data/versions.json akeneo@$${DEPLOY_HOSTNAME}:/var/www/
