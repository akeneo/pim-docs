Akeneo Custom Entity Bundle
===========================

The Custom Entity Bundle facilitates the creation of PIM reference data and related views in Akeneo.

Requirements
************

The bundle is not synchronized with the Akeneo development release cycle, look at this table
to choose the right version to install:

+-------------------------+-----------------------------------+
| **CustomEntityBundle**  | **Akeneo PIM Community Edition**  |
+-------------------------+-----------------------------------+
|         v1.8.*          |                v1.6.*             |
+-------------------------+-----------------------------------+
|         v1.7.*          |                v1.5.*             |
+-------------------------+-----------------------------------+
|         v1.6.*          |                v1.4.*             |
+-------------------------+-----------------------------------+
|         v1.5.0-RC1      |                v1.3.*             |
+-------------------------+-----------------------------------+
|         v1.4.*          |                v1.2.*             |
+-------------------------+-----------------------------------+
|         v1.3.*          |                v1.2.*             |
+-------------------------+-----------------------------------+
|         v1.2.*          |                v1.1.*             |
+-------------------------+-----------------------------------+
|         v1.1.*          |                v1.1.*             |
+-------------------------+-----------------------------------+

Installation
************

You can install this bundle with composer (see requirements section):

.. code-block:: bash

    composer require akeneo-labs/custom-entity-bundle:1.8.*

Then add the following lines **at the end** of your ``app/config/routing.yml``:

.. code-block:: yaml

    pim_customentity:
        prefix: /reference-data
        resource: "@PimCustomEntityBundle/Resources/config/routing.yml"

and enable the bundle in the ``app/AppKernel.php`` file in the ``registerBundles()`` method:

.. code-block:: php

    $bundles = [
        // ...
        new Pim\Bundle\CustomEntityBundle\PimCustomEntityBundle(),
    ]

.. include:: crud_interfaces.rst.inc

.. toctree::
   :hidden:

    crud_managers.rst
    abstract_entities_and_repositories.rst
