Product manager
===============

Prerequites
-----------

The Akeneo PIM project a service to manage your product entities.
This part of the application extends the `Oro Platform`_ bundle named `FlexibleEntityBundle`_.

.. _FlexibleEntityBundle: https://magecore.atlassian.net/wiki/display/DOC/OroFlexibleEntityBundle
.. _Oro Platform: http://www.orocrm.com/oro-platform

As stated above, product manager is a service you can get from the symfony container.
You just have to call `pim_catalog.manager.product` from a ContainerInterface class as below:

.. code-block:: php

    $this->container->get('pim_catalog.manager.product');

How to programmatically manipulate products
-------------------------------------------

In the following examples, we will use $pm as the product manager object.

* Create a product with an identifier value

.. code-block:: php

    // create a product
    $product = $pm->createFlexible();

    // create an identifier attribute
    $attribute = $pm->createAttribute('pim_catalog_identifier');
    $attribute->setCode('sku');

    // link product and attribute
    $pm->addAttributeToProduct($product, $attribute);

    // assign a sku value
    $product->setSku('akeneo-001');

    // get the value as ProductValueInterface or string
    $productValue = $product->getSku();
    $sku = (string) $product->getSku();

* Create an option

In some case, you can want to provide some possible values to a product.
Here an example to create t-shirt with different colors.

.. code-block:: php

   // create a color attribute
   $att = $pm->createAttribute('pim_catalog_simpleselect');
   $att->setCode('color');

   // create option values linked to the attribute
   $opt1 = $pm->createAttributeOption();
   $opt1->setCode('purple');
   $att->addOption($opt1);

   $opt2 = $pm->createAttributeOption();
   $opt2->setCode('yellow');
   $att->addOption($opt2);

   $opt3 = $pm->createAttributeOption();
   $opt3->setCode('blue');
   $att->addOption($opt3);

   // link product and attribute
   $product = $pm->createFlexible();
   $pm->addAttributeToProduct($product, $att);

   // assign a color
   $product->setColor($opt1);

   // returns [purple]
   echo $product->getColor();

* Translate your datas.

Keeping the example of a color,
the option value `purple` is "Purple" in english and "Violet" in french.

.. code-block:: php

    // create option values with i18n linked to the attribute
    $opt1 = $pm->createAttributeOption();
    $opt1->setCode('purple');
    $opt1->setTranslatable(true);

    $opt1EN = $pm->createAttributeOptionValue();
    $opt1EN->setLocale('en_US');
    $opt1EN->setValue('Purple');
    $opt1->addOptionValue($opt1EN);

    $opt1FR = $pm->createAttributeOptionValue();
    $opt1FR->setLocale('fr_FR');
    $opt1FR->setValue('Violet');
    $opt1->addOptionValue($opt1FR);

    $att->addOption($opt1);

    // ... do the same for $opt2

    echo $product->getColor(); // returns "Purple"

    $product->getColor()->getOption()->setLocale('fr_FR');
    echo $product->getColor(); // returns "Violet"

* Localize a product

A product can have different values depending of the locale:

* Manage scopes

* Manage translations + scopes





How to redefine your own product manager
----------------------------------------

You can easily redefine your own product manager with Sf2 DIC.
You just have to extends Akeneo PIM Catalog bundle and change
`pim_catalog.manager.product.class` parameter in `parameters.yml` file.

.. code-block:: yaml
parameters:
    pim_catalog.manager.product.class: MyProject\Bundle\CatalogBundle\Manager\ProductManager

Now you've to create your ProductManager class extending Akeneo PIM ProductManager.

How to redefine my own classes
------------------------------

The FlexibleEntityBundle from Oro Platform provides a dynamic attributes management, different
values storage and querying and form binding and validation.

It uses a configuration file to define the different parts of the EAV schema.




