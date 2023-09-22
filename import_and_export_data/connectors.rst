Akeneo Connectors
=================

CSV Connector
-------------

Akeneo CSV Connector supports reading and writing CSV files. The connector uses an open source library called openspout (https://github.com/openspout/openspout) to read and create CSV files.

XLSX Connector
--------------

Akeneo XLSX Connector supports reading and writing XLSX files. The connector uses an open source library called openspout (https://github.com/openspout/openspout) to read and create XLSX files.

The connector supports the following Excel versions:

- Windows Excel 2016
- Windows Excel 2013
- Windows Excel 2010
- Windows Excel 2007
- Excel 16 (Office 2016 for OS X)
- Excel 14 (Office 2011 for OS X)

YAML Connector
--------------

Akeneo YAML Connector supports reading and writing YAML files. The connector uses an open source component called symfony/yaml (https://github.com/symfony/yaml) to read and create YAML files.

Customizing connectors
----------------------

It is possible to customize these connectors to import and export numerous PIM related objects:

- Check how to import PIM objects using the Akeneo Connectors (cf :doc:`/import_and_export_data/simple-import`)
- In order to understand how the Akeneo CSV Connector is working for products, please refer to chapters about product import (cf :doc:`/import_and_export_data/product-import`) and product export (cf :doc:`/import_and_export_data/product-export`)
