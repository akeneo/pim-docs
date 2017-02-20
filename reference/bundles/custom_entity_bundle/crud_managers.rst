CRUD managers
=============

The bridge between entity / document managers is done with CRUD managers.

If you need to implement other backends, or specific business logic, you can create your own manager
by implementing the ``Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface`` interface, or overriding
the ``Pim\Bundle\CustomEntityBundle\Manager\Manager`` class.

Your manager can be registered by using the pim_custom_entity.manager tag in your DI:


.. code-block:: yaml

    services:
        acme_catalog.manager.my_manager:
            class: Acme\Bundle\CatalogBundle\Manager\MyManager
            tags:
                - { name:   pim_custom_entity.manager, alias: my_manager }


The manager for each class can be specified in your ``custom_entities.yml`` files:


.. code-block:: yaml

    custom_entities:
        my_entity:
            class: Acme\Bundle\CatalogBundle\Entity\MyEntity
            options:
                manager: my_manager
