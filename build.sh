#!/bin/bash -e

sphinx-build -b html /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-build
cp -L -r /home/akeneo/pim-docs/pim-community-dev-master/public /home/akeneo/pim-docs/data/pim-docs-build/
cp -r /home/akeneo/pim-docs/data/design_pim/styleguide /home/akeneo/pim-docs/data/pim-docs-build/design_pim/
