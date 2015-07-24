How to Create Non-Product Objects
=================================

To create common objects, we rely on different methods depending on the complexity of the object, there is few examples on how to use these services.

.. note::

    To ease the override of any object in your projects, we avoid any direct use of 'new MyObject()' in our code. We rely on services which implement a "creational pattern" and where we inject a config parameter, for instance, 'pim_catalog.entity.family.class'.

Use the AttributeFactory to Create a New Attribute
--------------------------------------------------

The ``Pim\Bundle\CatalogBundle\Factory\AttributeFactory`` is available as a service, you can fetch it from the container.

.. code-block:: php

    // fetch the factory from the container
    $factory = $this->getContainer()->get('pim_catalog.factory.attribute');

    // create an attribute with the attribute type
    $attribute = $factory->createAttribute('pim_catalog_text');
    $attribute->setCode('mynewattribute');

.. note::

    ``Pim\Bundle\CatalogBundle\Resources\config\attribute_types.yml`` contains the list of default attribute types, the alias of a service tagged with 'pim_catalog.attribute_type' is an attribute type useable here, for instance, 'pim_catalog_identifier', 'pim_catalog_text', 'pim_catalog_textarea', etc

Use the FamilyFactory to Create a New Family
--------------------------------------------

The ``Pim\Bundle\CatalogBundle\Factory\FamilyFactory`` is available as a service, you can fetch it from the container.

.. code-block:: php

    // fetch the factory from the container
    $factory = $this->getContainer()->get('pim_catalog.factory.family');

    // create an family, internally, the attribute requirement for the identifier attribute is directly added
    $family = $factory->createFamily();
    $family->setCode('mynewfamilycode');

Use the CategoryFactory to Create a New Category
------------------------------------------------

The ``Pim\Bundle\CatalogBundle\Factory\CategoryFactory`` is available as a service, you can fetch it from the container.

.. code-block:: php

    // fetch the factory from the container
    $factory = $this->getContainer()->get('pim_catalog.factory.category');

    // create an category
    $category = $factory->createCategory();
    $category->setCode('mynewcategorycode');

.. note::

    The same approach is used for other business objects
