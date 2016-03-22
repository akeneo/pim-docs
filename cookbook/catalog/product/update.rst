How to Update Products
======================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you to manage your product objects.

As the product model is the main object of the application, the use cases are more advanced than for simple models,

* a set of property updates on a single product (case of Product Edit Form)
* a different set of property updates on many products (case of Product Import)
* a similar set of property updates on many products (case of Bulk Action)
* [put your own logic here]

We provide different services to allow you to assemble your custom "update logic".

The default case is covered by ``Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface``, used to apply a set of property updates on a product.

You'll also find some services to update a single doctrine field data or an attribute data of a product (we hide this implementation detail, attribute vs doctrine field, under the term 'property').

There are four services to handle this case,

* ``Akeneo\Component\StorageUtils\Updater\PropertySetterInterface``
* ``Akeneo\Component\StorageUtils\Updater\PropertyAdderInterface``
* ``Akeneo\Component\StorageUtils\Updater\PropertyRemoverInterface``
* ``Akeneo\Component\StorageUtils\Updater\PropertyCopierInterface``

Instantiate a New ObjectUpdaterInterface
----------------------------------------

The product updater ``Pim\Component\Catalog\Updater\ProductUpdater`` implements ``Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface``.

The product updater is available as a service, you can fetch it from the container.

.. code-block:: php

    $updater = $this->getContainer()->get('pim_catalog.updater.product');

.. note::

    We provide several services which implement the same interface to update other business models (family, attribute, etc).

.. warning::

   The updater does not validate and save the products in the database, these operations are done by the Validator and the Saver (detailed in dedicated chapters).

   We target to respect the Single Responsibility Principle (SRP) in our classes, feel free to use these different services through a `Facade` if it facilitates your developments.

Use the ObjectUpdaterInterface
------------------------------

Now we can apply many property updates on a product.

.. code-block:: php

    $fieldUpdates = [
        "sku"         => [["locale" => null, "scope" => null, "data" => "MySku"]],
        "description" => [["locale" => "en_US", "scope" => "mobile", "data" => "My description"]]
        "categories"  => ["tshirt", "promo"],
    ];
    $this->updater->update($product, $fieldUpdates);

.. note::

    The standard array format used for property updates can be obtained by normalizing product values like this,
    "$container->get('pim_serializer')->normalize($values, 'json', ['entity' => 'product'])".

Instantiate a New PropertySetterInterface
-----------------------------------------

The product property setter implements ``Akeneo\Component\StorageUtils\Updater\PropertySetterInterface``.

It's available as a service, you can fetch it from the container.

.. code-block:: php

    $propertySetter = $this->getContainer()->get('pim_catalog.updater.product_property_setter');

Use the PropertySetterInterface
-------------------------------

The property setter allows to set a single property (doctrine field data or attribute data).

The property setter replaces the data if it already exists.

.. code-block:: php

    // sets data to the product name (an attribute)
    $propertySetter->setData($product, 'name', 'my name', ['locale' => 'en_US', 'scope' => 'mobile']);

    // sets data to the product categories (a field)
    $propertySetter->setData($product, 'categories', ['category_code1', 'category_code2']);

    // sets localizable and scopable attribute's data
    $propertySetter->setData($product, 'description', 'my description', ['locale' => 'en_US', 'scope' => 'mobile']);

.. note::

    This service has been reviewed in the 1.4 and now supports doctrine fields (before the 1.4, it was only available for attribute values).

    The method ``Pim\Bundle\CatalogBundle\Updater\ProductUpdater::setValue()`` is now deprecated, you should use ``Akeneo\Component\StorageUtils\Updater\PropertySetterInterface::setData()`` instead.

Instantiate a New PropertyAdderInterface
-----------------------------------------

The product property adder implements ``Akeneo\Component\StorageUtils\Updater\PropertyAdderInterface``.

It's available as a service, you can fetch it from the container.

.. code-block:: php

    $propertyAdder = $this->getContainer()->get('pim_catalog.updater.product_property_adder');

Use the PropertyAdderInterface
------------------------------

The property adder allows to add an item to a single property (doctrine field data or attribute data) which contains a collection.

This is only available for properties that contain several items (like categories, options, prices).

The property adder keeps the existing items and adds a new item to the set.

.. code-block:: php

    // adds data to product colors (a multiselect attribute)
    $propertyAdder->addData($product, 'color', ['red']);

    // adds data to product categories (a field)
    $propertyAdder->addData($product, 'category', ['tshirt', 'promo']);

Instantiate a New PropertyRemoverInterface
------------------------------------------

The product property remover implements ``Akeneo\Component\StorageUtils\Updater\PropertyRemoverInterface``.

It's available as a service, you can fetch it from the container.

.. code-block:: php

    $propertyRemover = $this->getContainer()->get('pim_catalog.updater.product_property_remover');

Use the PropertyRemoverInterface
--------------------------------

The property remover allows to remove an item from a single property (doctrine field data or attribute data) which contains a collection.

This is only available for properties that contain several items (like categories, options, prices).

The property remover keeps the existing items and removes only the provided item.

.. code-block:: php

    // removes the item "red" from product colors (a multiselect attribute)
    $propertyRemover->removeData($product, 'color', ['red']);

    // removes the product from the category "promo" (a field)
    $propertyRemover->removeData($product, 'category', ['promo']);

Instantiate a New PropertyCopierInterface
-----------------------------------------

The product property copier implements ``Akeneo\Component\StorageUtils\Updater\PropertyCopierInterface``.

It's available as a service, you can fetch it from the container.

.. code-block:: php

    $propertyCopier = $this->getContainer()->get('pim_catalog.updater.product_property_copier');

Use the PropertyCopierInterface
-------------------------------

The property remover allows to copy a data from a property to another property (doctrine field data or attribute data).

.. code-block:: php

    // copy the English name of the $fromProduct to the English description of the $toProduct
    // note that from_scope and to_scope are also available for scopable attributes
    $propertyCopier->copyData(
        $fromProduct,
        $toProduct,
        'name',
        'description',
        ['from_locale' => 'en_US', 'to_locale' => 'en_US']
    );

.. note::

    This service has been reviewed in the 1.4 version and now supports doctrine fields (before the 1.4, it was only available for attribute values).

    The method ``Pim\Bundle\CatalogBundle\Updater\ProductUpdater::copyValue()`` is now deprecated, you should use ``Akeneo\Component\StorageUtils\Updater\PropertyCopierInterface::copyData()``.

Add a Custom FieldSetterInterface
---------------------------------

If you create a new type of Attribute you need to implement the related ``Pim\Component\Catalog\Updater\Setter\AttributeSetterInterface``.

If you add a doctrine field in the Product model, you need to implement the related ``Pim\Component\Catalog\Updater\Setter\FieldSetterInterface``.

Both of these interfaces extend ``Pim\Component\Catalog\Updater\Setter\SetterInterface``.

A setter must implement this interface and be declared as a tagged service with the tag 'pim_catalog.updater.setter'.

Through a compiler pass, this service is finally registered in the setter registry ``Pim\Component\Catalog\Updater\Setter\SetterRegistry``.

This registry is used by the product updater to know how to update a product property.

For example, assuming that you have your own 'acme_catalog_number' attribute type, once implemented, you could declare your setter like this:

.. code-block:: yaml

    acme_catalog.updater.setter.number_value:
        class: Acme\Bundle\CatalogBundle\Updater\Setter\CustomNumberValueSetter
        parent: pim_catalog.updater.setter.abstract
        arguments:
            - ['acme_catalog_number']
        tags:
            - { name: 'pim_catalog.updater.setter' }

.. note::

    The best way to achieve your goal is to take a look at an existing implementation and try to find one that resembles what you want to achieve.

Add a Custom FieldAdderInterface
--------------------------------

The architecture of this part is very similar to the FieldSetterInterface case, you can refer to it.

It uses its own interfaces and the service tag 'pim_catalog.updater.adder'.

Add a Custom FieldRemoverInterface
----------------------------------

The architecture of this part is very similar to the FieldSetterInterface case, you can refer to it.

It uses its own interfaces and the service tag 'pim_catalog.updater.remover';

Add a Custom FieldCopierInterface
---------------------------------

The architecture of this part is very similar to the FieldSetterInterface case, you can refer to it.

It uses its own interfaces and the service tag 'pim_catalog.updater.copier';
