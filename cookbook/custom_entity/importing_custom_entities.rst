How to import fixtures for your custom entity :
===============================================

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
   :lines: 1,5
   :linenos: 

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
