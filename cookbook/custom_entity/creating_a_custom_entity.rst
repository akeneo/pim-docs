Create a Custom Entity and the Grid to Manage it
===================================================
  
Creating the Entity
-------------------
We wiil start by creating a classic doctrine entity wich extends one of the entity provided by CustomEntityBundle. 

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Entity/Supplier.php
   :language: php
   :prepend: # /src/Acme/Bundle/CatalogBundle/Entity/Supplier.php
   :linenos:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Entity/SupplierTranslation.php
   :language: php
   :prepend: # /src/Acme/Bundle/CatalogBundle/Entity/SupplierTranslation.php
   :linenos:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/Supplier.orm.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/Supplier.orm.yml
   :linenos:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/SupplierTranslation.orm.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/SupplierTranslation.orm.yml
   :linenos:

.. note::
    The extended CustonEntityBundle class provides a code and a translatable label attribute. 
    Several abstract entity and repositories are available in this bundle to help you with different
    requirements.

    These following classes are documented in `GitHub project repository <https://github.com/akeneo/CustomEntityBundle/blob/v1.5.0-RC1/Resources/doc/abstract_entities_and_repositories.rst>`_.

Creating the Entity Management
------------------------------
The Grid
********

To benefit from the grid component (which comes natively with filtering and sorting),
you can define the vendor grid as following:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/datagrid.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/datagrid.yml
   :linenos:


The Form Type
*************

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Form/Type/ColorType.php
   :language: php
   :prepend: # /src/Acme/Bundle/EnrichBundle/Form/Type/ColorType.php
   :linenos:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/form_types.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/form_types.yml
   :linenos:


The CRUD
********

A complete CRUD can be easily obtained by adding a custom_entities.yml file in one of your bundles.

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/custom_entities.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/custom_entities.yml
   :linenos:

From this point a working grid screen is visible at ``/app_dev.php/enrich/color``.

If some vendors are manually added to the database, the pagination will be visible as well.

.. note::
   Have a look at the Cookbook recipe "How to add an menu entry" to add your own link in the menu to this grid.

.. _`akeneo/custom-entity-bundle`: https://packagist.org/packages/akeneo/custom-entity-bundle
