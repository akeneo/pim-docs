How to use updaters
===================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your product entities.

The updaters allows you to set and copy values from product's attributes to another product's attributes with a simple
API.

Instantiate a new updater
-------------------------

.. code-block:: php

    // In order to use updater we need to get the updater service
    $updater = $this->getContainer()->get('pim_catalog.updater.product');


Use setters and copiers
-----------------------

Setters
-------

In order to use setters you will need to use the 'setValue()' method. The first argument correspond to all the products
you want to set a new value, the second argument is the attribute type, the third argument is the value to set (it
has to be compliant with the attribute type), then you can specify the locale and the scope (it depends on the
attribute, but sometime it's required and sometime you can sets it as null and it will be applied on all locales and
all scopes).

.. code-block:: php

    $updater
        ->setValue($products, 'name', 'Akeneo T-Shirt (new)')
        ->setValue($products, 'description', 'Akeneo T-Shirt white with short sleeve (new)', 'en_US', 'ecommerce')
        ->setValue($products, 'price', [['data' => 101, 'currency' => 'USD', 'fr_FR', 'mobile']]);

Copiers
-------

Copier mechanism is very close to the setter one, in order to use them you need to call the 'copyValue()' method. The
first argument remains the same as setter, the second and the third arguments correspond the the type of the
attributes (the second one correspond to the copied argument and the third one correspond to the argument value
destination, both of them have to be compliant and have to be supported by the copier. You can mix argument types id
they are compliant and supported by the copier).

.. code-block:: php

    $updater
        ->copyValue($products, 'description', 'description', 'en_US', 'en_US', 'ecommerce', 'mobile')

Don't forget to flush the changes:

.. code-block:: php

    // Don't forget to flush with doctrine
    $om = $this->getContainer()->get('pim_catalog.object_manager.product');
    $om->flush();

Add a custom setter
-------------------

In order to use a custom setter you will need to inherit your setter from the
Pim\Bundle\CatalogBundle\Updater\Setter\AbstractValueSetter and implement you custom logic in the setValue() method.

Then you will need to declare this setter as a service:

.. code-block:: yaml

    pim_catalog.updater.setter.number_value:
        class: %pim_catalog.updater.setter.number_value.class%
        arguments:
            - @pim_catalog.builder.product
            - ['pim_catalog_number']
        tags:
            - { name: 'pim_catalog.updater.setter' }

Don't forget to add the supported attribute type(s) as a parameter. Here the setter supports only the
'pim_catalog_number' type

Add a custom copier
-------------------

In order to use a custom copier you will need to inherit your copier from the
Pim\Bundle\CatalogBundle\Updater\Copier\AbstractValueCopier and implement you custom logic in the copyValue() method.

Then you will need to declare this copier as a service:

.. code-block:: yaml

    pim_catalog.updater.copier.number_value:
        class: %pim_catalog.updater.copier.number_value.class%
        arguments:
            - @pim_catalog.builder.product
            - ['pim_catalog_number']
        tags:
            - { name: 'pim_catalog.updater.copier' }

Don't forget to add the supported attribute type(s) as a parameter. Here the copier supports only the
'pim_catalog_number' type.
