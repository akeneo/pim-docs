How to Customize Import / Export
================================

This chapter describes how to create and customize connectors to handle imports and exports in your Akeneo PIM project.

.. note::

  The import part has been widely re-worked in 1.4.

  A new system has been introduced, the old system has been deprecated but is kept, both systems are useable in 1.4 but we strongly recommend using the new system.

  The new system is composed of a new Connector Bundle, a new Connector Component and some new Catalog APIs.
  The deprecated system consists of parts of the BaseConnector Bundle and parts of the Transform Bundle (we aim to depreciate entirely these bundles).

.. toctree::

   main-concepts
   create-connector
   product-import
   create-specific-connector
   create-custom-step
   customize-import-behavior
   mongodb-fast-writer
