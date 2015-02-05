#!/bin/sh
echo "Removing existing files within ../pim-docs-build/*"
rm ../pim-docs-build/* -rf;
echo "Generting docs in ../pim-docs-build/*"
sphinx-build -b html . ../pim-docs-build/;
