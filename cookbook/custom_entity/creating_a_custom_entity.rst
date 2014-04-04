How to Create a Custom Entity and the Screens to Manage it
==========================================================

.. note::
    The code inside this cookbook entry is visible in src directory, you can clone pim-dev then do a symlink and install

.. note::
    The code inside this chapter requires you to install the `akeneo/custom-entity-bundle`_ package.

Creating the Entity
-------------------


As Akeneo relies heavily on standard tools like Doctrine, creating the entity is
quite straightforward for any developer with Doctrine experience.

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Entity/Color.php
   :language: php
   :prepend: # /src/Acme/Bundle/CatalogBundle/Entity/Color.php
   :linenos:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/Color.orm.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/doctrine/Color.orm.yml
   :linenos:

.. note::
    To ease the integration of the entity in the PIM, we extended an abstract class from CustomEntityBundle.
    Several abstract entity and repositories are available in this bundle to help you with different
    requirements.

Creating the Entity Management Screens
--------------------------------------
The Grid
********

To benefit from the grid component (which comes natively with filtering and sorting), 
you can define the vendor grid as following :

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/datagrid.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/datagrid.yml
   :linenos:


Creating the Form Type for this Entity
**************************************

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Form/Type/ColorType.php
   :language: php
   :prepend: # /src/Acme/Bundle/EnrichBundle/Form/Type/ColorType.php
   :linenos:


Creating the CRUD
*****************

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