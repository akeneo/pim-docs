#!/bin/bash -e

usage(){
    echo "Usage: $0 <version> [--uid <uid>] [--gid <gid]"
    echo "  version               The version to build (e.g. 1.7 or master)"
}

VERSION=$1; shift; echo "Building version $VERSION..."

rm -rf /home/akeneo/pim-docs/data/pim-docs-build/web
sed -i -e "s/^version =.*/version = '${VERSION}'/" /home/akeneo/pim-docs/data/conf.py

sphinx-build -b html /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-build
cp -L -r /home/akeneo/pim-docs/pim-community-dev-${VERSION}/public /home/akeneo/pim-docs/data/pim-docs-build/
cp -r /home/akeneo/pim-docs/data/design_pim/styleguide /home/akeneo/pim-docs/data/pim-docs-build/design_pim/
