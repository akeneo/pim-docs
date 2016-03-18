Import / Export
===============

This chapter describes the architecture and behaviour of imports and exports in your Akeneo PIM project.

.. note::

  The import part has been widely re-worked in 1.4.

  A new system has been introduced, the old system has been deprecated but is kept, both systems are usable in 1.4 but we strongly recommend using the new one.

  The new system is composed of a new Connector Bundle, a new Connector Component and some new Catalog APIs.
  The deprecated system consists of parts of the BaseConnector Bundle and parts of the Transform Bundle (we aim to depreciate entirely these bundles).

.. note::

  You can take a look at the cookbook section to practise through examples :doc:`/cookbook/import_export/index`

.. toctree::
    :maxdepth: 2

    main-concepts
    product-import
    product-export
    connectors
