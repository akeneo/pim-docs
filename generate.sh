#!/bin/sh
echo "Removing existing files within ../pim-docs-build/*"
rm -rf ../pim-docs-build/*;
echo "Generting docs in ../pim-docs-build/*"
sphinx-build -b html . ../pim-docs-build/;
echo "Copying images."
cp _images/logo.png ../pim-docs-build/_images/
echo "Done."

