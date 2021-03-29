Troubleshooting guide
=====================

Auto_increment reaches max value for pim_catalog_completeness table
-------------------------------------------------------------------

.. note::

   Impacted versions: PIM <= 5.0

After some time, the completeness table auto_increment identifier could reach the defined maximum.
We made a fix to to allow for a lot more identifier in PIM-9750 ticket but for older database schema,
you must apply this fix with the following MySql ALTER command:

.. code-block:: sql

   ALTER TABLE pim_catalog_completeness MODIFY id bigint NOT NULL AUTO_INCREMENT;


Asset family identifiers stored with a different case in attributes properties (EE only)
----------------------------------------------------------------------------------------

.. note::

   Impacted versions: PIM EE <= 5.0

We fixed the attributes import to sanitize the asset family identifiers in PIM-9753,
and you may need to fix data already stored in database with the following clean command:

.. code-block:: bash

   bin/console --env=prod pim:asset-manager:clean-asset-family-in-asset-collection-attributes


Asset & Asset family codes being case-sensitive (EE only)
----------------------------------------------------------------------------------------

.. note::

   Impacted versions: PIM EE <= 5.0

The Asset index mapping has been updated to be case-insensitive on the Asset & Asset family codes.
You may need to reset your index and re-index all Assets:

.. code-block:: bash

   bin/console akeneo:elasticsearch:reset-indexes --index=akeneo_assetmanager_asset
   bin/console akeneo:asset-manager:index-assets --all
