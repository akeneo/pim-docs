UID = $(shell id -u)
GID = $(shell id -g)
DOCKER_IMAGE = pim-docs
DOCKER_PIM_DOCS_RUN = docker run -it --rm -u $(UID):$(GID) -v $(PWD):/home/akeneo/pim-docs/data
DOCKER_PIM_CE_RUN= docker run -it --rm -u $(UID):$(GID) -v $(PWD)/_build:/home/akeneo/ce
DOCKER_RSYNC = $(DOCKER_PIM_DOCS_RUN) -v /etc/passwd:/etc/passwd:ro -v $${SSH_AUTH_SOCK}:/ssh-auth.sock:ro -e SSH_AUTH_SOCK=/ssh-auth.sock $(DOCKER_IMAGE) rsync -e "ssh -q -p $${DEPLOY_PORT} -o StrictHostKeyChecking=no" -qarz --delete

.DEFAULT_GOAL := build
.PHONY: build, deploy, docker-build, update-versions

build: lint
	# Flags used here, not in `make html`:
	#  -n   Run in nit-picky mode. Currently, this generates warnings for all missing references.
	#  -W   Turn warnings into errors. This means that the build stops at the first warning and sphinx-build exits with exit status 1.
	#  -T   Displays the full stack trace if an unhandled exception occurs.
	#  -b   Linkcheck builder checks for broken links.
	$(DOCKER_PIM_DOCS_RUN) $(DOCKER_IMAGE) sphinx-build -nWT -b html /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-build
	cp -L -r ./_build/pim-community-dev/public ./pim-docs-build/
	@echo "\nYou are now ready to check the documentation locally in the directory \"pim-docs-build/\" and to deploy it with \"DEPLOY_HOSTNAME=foo.com DEPLOY_PORT=1985 VERSION=bar make deploy\"."

deploy: build update-versions
	$(DOCKER_PIM_DOCS_RUN) -v /etc/passwd:/etc/passwd:ro -v $${SSH_AUTH_SOCK}:/ssh-auth.sock:ro -e SSH_AUTH_SOCK=/ssh-auth.sock $(DOCKER_IMAGE) rsync -e "ssh -q -p $${DEPLOY_PORT} -o StrictHostKeyChecking=no" -qarz --delete /home/akeneo/pim-docs/data/pim-docs-build/ akeneo@$${DEPLOY_HOSTNAME}:/var/www/${VERSION}

lint: dependencies
	rm -rf pim-docs-build && mkdir pim-docs-build
	rm -rf pim-docs-lint && mkdir pim-docs-lint
	$(DOCKER_PIM_DOCS_RUN) -v $(PWD):/home/akeneo/pim-docs/data $(DOCKER_IMAGE) sphinx-build -nWT -b linkcheck /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-lint

dependencies: docker-build
	rm -rf _build && mkdir _build
	$(DOCKER_PIM_CE_RUN) $(DOCKER_IMAGE) wget https://github.com/akeneo/pim-community-dev/archive/5.0.zip -P /home/akeneo/ce
	$(DOCKER_PIM_CE_RUN) -w /home/akeneo/ce $(DOCKER_IMAGE) unzip /home/akeneo/ce/5.0.zip
	mv _build/pim-community-dev-5.0 _build/pim-community-dev
	$(DOCKER_PIM_CE_RUN) -w /home/akeneo/ce/pim-community-dev $(DOCKER_IMAGE) php -d memory_limit=3G /usr/local/bin/composer install --no-suggest --ignore-platform-reqs
	$(DOCKER_PIM_CE_RUN) -w /home/akeneo/ce/pim-community-dev $(DOCKER_IMAGE) php bin/console pim:installer:assets --env=prod
	mkdir $(PWD)/_build/pim-community-dev/public/css
	$(DOCKER_PIM_CE_RUN) $(DOCKER_IMAGE) wget  http://demo.akeneo.com/css/pim.css -P /home/akeneo/ce/pim-community-dev/public/css

docker-build:
	docker build . --tag $(DOCKER_IMAGE)

update-versions:
	$(DOCKER_RSYNC) akeneo@$${DEPLOY_HOSTNAME}:/var/www/versions.json /home/akeneo/pim-docs/data
	$(DOCKER_PIM_DOCS_RUN) -w /home/akeneo/pim-docs/data $(DOCKER_IMAGE) php scripts/update-doc-versions.php $(CIRCLE_BRANCH) versions.json
	$(DOCKER_RSYNC) /home/akeneo/pim-docs/data/versions.json akeneo@$${DEPLOY_HOSTNAME}:/var/www/
