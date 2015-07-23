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

The default case is covered by the use of an ObjectUpdaterInterface to apply a set of property updates on a product.

You'll find also some services to update a single doctrine field data or an attribute data of a product (we hide this implementation detail, attribute vs doctrine field, under the term 'property').

There are four services to handle this, a PropertySetterInterface, a PropertyAdderInterface, a PropertyRemoverInterface and a PropertyCopierInterface.

Instantiate a New ObjectUpdaterInterface
----------------------------------------

The product updater ``Pim\Component\Catalog\Updater\ProductUpdater`` implements the ``Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface``.

The product updater is available has a service, you can fetch it from the container.

.. code-block:: php

    $updater = $this->getContainer()->get('pim_catalog.updater.product');

.. note::

    We provide several services which implements `ObjectUpdaterInterface` to manipulate other business models (family, attribute, etc).

.. warning::

   The updater does not validate and save the products in the database, these operations are done by the Validator and the Saver (detailed in specific chapters).

   We target to respect the Single Responsibility Principle (SRP) in our classes, feel free to use these different services through a `Facade` if it ease your developments.

Use the Object Updater
----------------------

Then we can apply many property updates on a product.

.. code-block:: php

    $fieldUpdates = [
        "sku": [["locale": null, "scope":  null, "data":  "MySku"]],
        "description": [["locale": "en_US", "scope": "mobile", "data": "My description"]]
        "categories": ["tshirt", "promo"],
    ];
    $this->updater->update($product, $fieldUpdates);

.. note::

    The standard array format used for property updates can be obtained by normalizing product values like this,
    "$container->get('pim_serializer')->normalize($values, 'json', ['entity' => 'product'])".

Instanciate a New PropertySetterInterface
-----------------------------------------

The product property setter implements the ``Akeneo\Component\StorageUtils\Updater\PropertySetterInterface``.

It's available has a service, you can fetch it from the container.

.. code-block:: php

    $propertySetter = $this->getContainer()->get('pim_catalog.updater.product_property_setter');

Use the PropertySetterInterface
-------------------------------

The property setter allows to set a single property (doctrine field data or attribute data).

The property setter replace the data if it already exists.

.. code-block:: php

    // sets a data in the product name (an attribute)
    $propertySetter->setData($product, 'name', 'my name');

    // sets a data in the product name (a field)
    $propertySetter->setData($product, 'categories', ['category_code1', 'category_code2']);

    // sets a localizable and scopable product attribute data
    $propertySetter->setData($product, 'description', 'my description', ['locale' => 'en_US', 'scope' => 'mobile']);

.. note::

    This service has been reworked since the 1.3 to add the support of doctrine fields, become more complete and consistent with other property updaters.

    The method 'ProductUpdater::setValue()' is now deprecated, you should use the `PropertySetterInterface::setData()`.

Instanciate a New PropertyAdderInterface
-----------------------------------------

The product property adder implements the ``Akeneo\Component\StorageUtils\Updater\PropertyAdderInterface``.

It's available has a service, you can fetch it from the container.

.. code-block:: php

    $propertyAdder = $this->getContainer()->get('pim_catalog.updater.product_property_adder');

Use the PropertyAdderInterface
------------------------------

The property adder allows to add an item in a single property (doctrine field data or attribute data) which contain a collection.

This one is only available for properties that contains many items (as categories, options, prices).

The property adder keep the existing items and add a new item inside.

.. code-block:: php

    // adds a data in the product color (a multiselect attribute)
    $propertyAdder->addData($product, 'color', ['red']);

    // adds a data in the product categories (a field)
    $propertyAdder->addData($product, 'category', ['tshirt', 'promo']);

Instanciate a New PropertyRemoverInterface
------------------------------------------

The product property remover implements the ``Akeneo\Component\StorageUtils\Updater\PropertyRemoverInterface``.

It's available has a service, you can fetch it from the container.

.. code-block:: php

    $propertyRemover = $this->getContainer()->get('pim_catalog.updater.product_property_remover');

Use the PropertyRemoverInterface
--------------------------------

The property remover allows to remove an item from a single property (doctrine field data or attribute data) which contain a collection.

This one is only available for properties that contains many items (as categories, options, prices).

The property remover keep the existing items and remove only the provided items.

.. code-block:: php

    // removes an item "red" from the data of the product color (a multiselect attribute)
    $propertyRemover->removeData($product, 'color', ['red']);

    // removes the product from the category "promo" (a field)
    $propertyRemover->removeData($product, 'category', ['promo']);

Instanciate a New PropertyCopierInterface
-----------------------------------------

The product property copier implements the ``Akeneo\Component\StorageUtils\Updater\PropertyCopierInterface``.

It's available has a service, you can fetch it from the container.

.. code-block:: php

    $propertyCopier = $this->getContainer()->get('pim_catalog.updater.product_property_copier');

Use the PropertyCopierInterface
-------------------------------

The property remover allows to copy a data from a property to another property (doctrine field data or attribute data).

.. code-block:: php

    // copy the english name of the $fromProduct to the english description of the $toProduct
    // note that from_scope and to_scope are also available for scopable attributes
    $propertyCopier->copyData(
        $fromProduct,
        $toProduct,
        'name',
        'description',
        ['from_locale' => 'en_US', 'to_locale' => 'en_US']
    );

.. note::

    This service has been reworked since the 1.3 to add the support of doctrine fields, become more complete and consistent with other property updaters.

    The method 'ProductUpdater::copyValue()' is now deprecated, you should use the `PropertyCopierInterface::copyData()`.

Add a Custom FieldSetterInterface
---------------------------------

If you create a new type of Attribute you need to implement the related ``Pim\Component\Catalog\Updater\Setter\AttributeSetterInterface``.

If you add a doctrine field in the Product model, you need to implement the related ``Pim\Component\Catalog\Updater\Setter\FieldSetterInterface``.

Both of these interfaces extends ``Pim\Component\Catalog\Updater\Setter\SetterInterface``.

A setter must implement this interface and be declared as a tagged service.

Through a compiler pass, this service is finaly registered in the setter registry ``Pim\Component\Catalog\Updater\Setter\SetterRegistry``.

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

    The best way to create you own is to take on look on existing implementation and try to find one close to the case you want achieve.

Add a Custom FieldAdderInterface
--------------------------------

The architecture of this part is very similar to the FieldSetterInterface case, you can refer to it.

It uses its own interfaces and service tag 'pim_catalog.updater.adder';

Add a Custom FieldRemoverInterface
----------------------------------

The architecture of this part is very similar to the FieldSetterInterface case, you can refer to it.

It uses its own interfaces and service tag 'pim_catalog.updater.remover';

Add a Custom FieldCopierInterface
---------------------------------

The architecture of this part is very similar to the FieldSetterInterface case, you can refer to it.

It uses its own interfaces and service tag 'pim_catalog.updater.copier';