# Flexibility / On-Premise documentation archive.
# See README.md for the layout of this branch.

OUTPUT_DIR ?= $(CURDIR)/public
PORT ?= 8000
export OUTPUT_DIR

.DEFAULT_GOAL := build
.PHONY: build build-version serve publish clean

## Build every version listed in build/versions.txt into $(OUTPUT_DIR)
build:
	scripts/build.sh

## Build only some versions, e.g. make build-version VERSIONS="7.0 6.0"
build-version:
	scripts/build.sh $(VERSIONS)

## Serve $(OUTPUT_DIR) on http://localhost:$(PORT)/latest/index.html
serve:
	python3 -m http.server --directory $(OUTPUT_DIR) $(PORT)

## Publish $(OUTPUT_DIR) to the site bucket (needs SITE_BUCKET, see scripts/publish.sh)
publish:
	scripts/publish.sh

clean:
	rm -rf $(OUTPUT_DIR)
