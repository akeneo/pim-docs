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

We fixed the attributes import to sanitize the asset family identifiers, but if you need to fix data already stored
in database, we provide a tooling command to clean this data:

.. code-block:: sql

   bin/console --env=prod pim:asset-manager:clean-asset-family-in-asset-collection-attributes
