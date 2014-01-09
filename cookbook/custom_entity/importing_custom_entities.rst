How to import fixtures for your custom entity and attribute
===========================================================

Implement ReferableInterface
----------------------------

To ensure your entity is imported correctly, the first step is to implement the following interfaces.

 Your entity should implement the ``Pim\Bundle\CatalogBundle\Model\ReferableInterface`` interface

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Entity/Vendor.php
   :language: php
   :lines: 1-5,8,17-18,183-
   :linenos:


Your entity repository should implement the ``Pim\Bundle\CatalogBundle\Entity\Repository\ReferableEntityRepositoryInterface`` interface,
or be a subclass of ``Pim\Bundle\CatalogBundle\Entity\Repository\ReferableEntityRepository``

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/doctrine/Vendor.orm.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/doctrine/Vendor.orm.yml
   :lines: 1,4
   :linenos:

Attribute value importation
---------------------------

If your entity implements the ``Pim\Bundle\CatalogBundle\Model\ReferableInterface`` interface and is linked
to ``ProductValue`` with a direct association of any cardinality, importation of product values requires
no configuration or coding on your side. Simply provide the reference to the linked entities in your import files,
and the importation will be done automatically.

If you have indirect associations, or if you cannot implement the interfaces in your entity and your repository,
you will have to create a specific property transformer for your attribute type. (see :doc:`../import_export/customize-import-behavior` for more explanations)

To use of your property transformer, you will have to configure a guesser service similar to the one used by
price collection attributes :

.. code-block:: yaml
    :linenos:

    # /vendor/akeneo/pim-community-dev/src/Pim/Bundle/ImportExportBundle/Resources/config/guessers.yml
    services:
        pim_import_export.transformer.guesser.prices:
            class: "%pim_import_export.transformer.guesser.attribute.class%"
            public: false
            arguments:
                - "@pim_import_export.transformer.property.prices"
                - "%pim_catalog.entity.product_value.class%"
                - prices
            tags:
                - { name: pim_import_export.transformer.guesser, priority: 40 }


Create a processor
------------------

If your entity and its associations implement the ``Pim\Bundle\CatalogBundle\Model\ReferableInterface``
interface, creating the processor is done by simply configuring a new service in your DIC :

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/processors.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/processors.yml
   :linenos:

Configure fixtures
------------------

The order and the processor for your fixtures is given in the ``fixtures.yml`` configuration file:

.. literalinclude:: ../../src/Pim/Bundle/IcecatDemoBundle/Resources/config/fixtures.yml
   :language: yaml
   :prepend: # /src/Pim/Bundle/IcecatDemoBundle/Resources/config/fixtures.yml
   :linenos:

You can now add a ``vendors.csv`` or a ``vendors.yml`` file in your fixtures folder, it will be
loaded with other fixtures. (see :doc:`../setup_data/customize_installer`)

Create a connector
------------------

The processor created in the first step can be used to create a full import/export connector.
Please read :doc:`../import_export/create-connector` for more details.
