How to Update Products
======================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your product entities.

The product updater allows to set values for many products, or copy a value to another value in many products

In the following examples, we will assume that we have a collection of one or many products in the variable
``$products``. The collection can be fetched from the database or newly created with the
``pim_catalog.builder.product`` service.

Instantiate a new updater
-------------------------

.. code-block:: php

    // The product updater is a service, you can fetch it from the container
    $updater = $this->getContainer()->get('pim_catalog.updater.product');

Use setters and copiers
-----------------------

Setters
-------

In order to use setters you will need to use the 'setValue()' method. The first argument matches all the products
you want to set a new value, the second argument is the attribute code, the third argument is the value to set (it
has to be compliant with the attribute type). Then you can specify the locale and the scope (locale and scope must be
used when the attribute is localizable or scopable).

.. code-block:: php

    $updater
        ->setValue($products, 'name', 'Akeneo T-Shirt (new)')
        ->setValue($products, 'description', 'Akeneo T-Shirt white with short sleeve (new)', 'en_US', 'ecommerce')
        ->setValue($products, 'price', [['data' => 101.2, 'currency' => 'USD']], 'en_US', 'mobile');

Copiers
-------

Copier mechanism is very close to the setter one, in order to use them you need to call the 'copyValue()' method. The
first argument remains the same as setter, the second and the third arguments matches the code of the attributes (the
second one matches to the copied argument and the third one correspond to the argument value destination,
both of them have to be compliant and have to be supported by the copier).

.. code-block:: php

    $updater
        ->copyValue($products, 'description', 'description', 'en_US', 'en_US', 'ecommerce', 'mobile')

Doctrine
--------

.. note:: Don't forget to flush the changes:

.. code-block:: php

    // To save the changes, save them with the product saver
    $saver = $this->getContainer()->get('pim_catalog.saver.product');
    $saver->save($product); //To save one product
    $saver->saveAll($products); //To save a collection of product

Add a custom setter
-------------------

In order to use a custom setter you will need to implement
``Pim\Bundle\CatalogBundle\Updater\Setter\ValueSetterInterface`` and create you custom logic in the setValue() method.

For example, if you want to implement your own number setter, you will need to declare this setter as a service:

.. code-block:: yaml

    acme_catalog.updater.setter.custom_number_value:
        class: Acme\Bundle\CatalogBundle\Updater\Setter\CustomNumberValueSetter
        arguments:
            - @acme_catalog.builder.product
            - ['acme_catalog_number']
        tags:
            - { name: 'acme_catalog.updater.setter' }

Don't forget to add the supported attribute type(s) as a parameter. Here the setter only supports the
'acme_catalog_number' type.

Add a custom copier
-------------------

In order to use a custom copier you will need to implement
``Pim\Bundle\CatalogBundle\Updater\Copier\ValueSetterInterface`` and implement you custom logic in the copyValue() method.

For example, if you want to implement your own number setter, you will need to declare your copier as a service:

.. code-block:: yaml

    acme_catalog.updater.copier.custom_number_value:
        class: Acme\Bundle\CatalogBundle\Updater\Copier\CustomNumberValueSetter
        arguments:
            - @acme_catalog.builder.product
            - ['acme_catalog_number']
        tags:
            - { name: 'acme_catalog.updater.copier' }

Don't forget to add the supported attribute type(s) as a parameter. Here the copier supports only the
``acme_catalog_number`` type.
