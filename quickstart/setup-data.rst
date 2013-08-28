Setup data
----------

The PIM comes from an installer bundle that allows to easily setup your own catalog data with locales, currencies, channels, families, attributes and attribute groups.

First, create AcmeInstallerBundle :
* create directory in src/Acme/Bundle/AcmeInstallerBundle
* create AcmeInstallerBundle.php :
TODO
* create AcmeInstallerBundle/Resources/config/
* copy / paste pim_installer_*.yml files from PimInstallerBundle
* change the yml files to describe your own catalog structure

Enable the bundle in your AppKernel.php

Runs a doctrine schema update then load fixtures (you can use init-db.sh script).

That's it, the PIM now contains your base catalog structure.
