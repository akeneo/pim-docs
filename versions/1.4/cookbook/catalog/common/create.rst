How to Create Non-Product Objects
=================================

To create common objects, we rely on different methods depending on the complexity of the object. There are some examples on how to use these services.

.. note::

    To make it easier to override an object in your projects, we avoid any direct usage of 'new MyObject()' in our code. We rely on services which implement a "creational pattern" where we inject a config parameter, for instance, 'pim_catalog.entity.family.class'.

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

    ``Pim\Bundle\CatalogBundle\Resources\config\attribute_types.yml`` contains the list of the default attribute types. The alias of a service tagged with 'pim_catalog.attribute_type' can be used here, for instance, 'pim_catalog_identifier', 'pim_catalog_text', 'pim_catalog_textarea', etc.

Use the FamilyFactory to Create a New Family
--------------------------------------------

The ``Pim\Bundle\CatalogBundle\Factory\FamilyFactory`` is available as a service, you can fetch it from the container.

.. code-block:: php

    // fetch the factory from the container
    $factory = $this->getContainer()->get('pim_catalog.factory.family');

    // create a family, internally, the attribute requirement for the identifier attribute is directly added
    $family = $factory->createFamily();
    $family->setCode('mynewfamilycode');

Use the CategoryFactory to Create a New Category
------------------------------------------------

The ``Pim\Bundle\CatalogBundle\Factory\CategoryFactory`` is available as a service, you can fetch it from the container.

.. code-block:: php

    // fetch the factory from the container
    $factory = $this->getContainer()->get('pim_catalog.factory.category');

    // create a category
    $category = $factory->createCategory();
    $category->setCode('mynewcategorycode');

.. note::

    The same approach is used for other business objects.
