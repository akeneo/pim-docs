How to Create Non-Product Objects
=================================

.. _SimpleFactoryInterface: https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Tool/Component/StorageUtils/Factory/SimpleFactoryInterface.php

To create common objects, we rely on different methods depending on the complexity of the object. There are some examples on how to use these services.

.. note::

    To make it easier to override an object in your projects, we avoid any direct usage of ``new MyObject()`` in our code. We rely on services which implement a "creational pattern" where we inject a config parameter, for instance, ``pim_catalog.entity.family.class``.

Akeneo PIM provides a Factory for almost every simple objects. Each factory inherits from the `SimpleFactoryInterface`_. Here we introduce how to use ``AttributeFactory``, ``FamilyFactory`` and ``CategoryFamily``, but the behavior is the same for all other factories.

Use the AttributeFactory to Create a New Attribute
--------------------------------------------------

The ``Akeneo\Pim\Structure\Component\Factory\AttributeFactory`` is available as a service, you can fetch it from the container.

.. code-block:: php

    // fetch the factory from the container
    $factory = $this->getContainer()->get('pim_catalog.factory.attribute');

    // create an attribute with the attribute type
    $attribute = $factory->create('pim_catalog_text');
    $attribute->setCode('mynewattribute');

.. note::

    ``src/Akeneo/Pim/Structure/Bundle/Resources/config/attribute_types.yml`` contains the list of the default attribute types. The alias of a service tagged with ``pim_catalog.attribute_type`` can be used here, for instance, ``pim_catalog_identifier``, ``pim_catalog_text``, ``pim_catalog_textarea``, etc.

Use the FamilyFactory to Create a New Family
--------------------------------------------

The ``Pim\Component\Catalog\Factory\FamilyFactory`` is available as a service, you can fetch it from the container.

.. code-block:: php

    // fetch the factory from the container
    $factory = $this->getContainer()->get('pim_catalog.factory.family');

    // create a family, internally, the attribute requirement for the identifier attribute is directly added
    $family = $factory->create();
    $family->setCode('mynewfamilycode');

Use the CategoryFactory to Create a New Category
------------------------------------------------

The category factory ``Akeneo\Tool\Component\StorageUtils\Factory\SimpleFactory`` is available as a service, you can fetch it from the container.

.. code-block:: php

    // fetch the factory from the container
    $factory = $this->getContainer()->get('pim_catalog.factory.category');

    // create a category
    $category = $factory->create();
    $category->setCode('mynewcategorycode');

.. note::

    The same approach is used for other business objects.
