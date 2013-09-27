How to programmatically manipulate products
===========================================

Prerequisites
-------------

The Akeneo PIM project introduces a service to help you manage your product entities.
This part of the application extends the `Oro Platform`_ bundle named `FlexibleEntityBundle`_.

.. _FlexibleEntityBundle: https://magecore.atlassian.net/wiki/display/DOC/OroFlexibleEntityBundle
.. _Oro Platform: http://www.orocrm.com/oro-platform

As stated above, the product manager is a service you can get from the symfony container :

.. code-block:: php

    $this->container->get('pim_catalog.manager.product');


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

In some cases, you will want to restrain values to a list of possibilities for a product attribute.
For instance, this example creates a color attribute with a list of predefined options :

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

Keeping the color example, the option value `purple` is "Purple" in English and "Violet" in French.

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

A product can have different values depending of the locale.
We considers that the locales `en_US` and `fr_FR` are already created and activated.

.. code-block:: php

    $pm = $this->productManager;
    $product = $pm->createFlexible();

    // create a localizable attribute
    $attribute = $pm->createAttribute('pim_catalog_text');
    $attribute->setCode('name');
    $attribute->setTranslatable(true);

    $pm->addAttributeToProduct($product, $attribute);

    $product->setName('My name', 'en_US');
    $product->setName('Mon nom', 'fr_FR');

    echo $product->getName(); // returns "My name"

    $product->setLocale('fr_FR');
    echo $product->getName(); // returns "Mon nom"

* Manage scopes

Akeneo PIM is a multi-channels application so you can define different scopes to use.
We considers that channels `ecommerce` and `mobile` are already created.

.. code-block:: php

    $pm = $this->productManager;
    $product = $pm->createFlexible();

    // create a scopable attribute
    $attribute = $pm->createAttribute('pim_catalog_text');
    $attribute->setCode('image_hd');
    $attribute->setScopable(true);

    $pm->addAttributeToProduct($product, $attribute);

    $product->setImageHd('my_ecommerce_image', null, 'ecommerce');
    $product->setImageHd('my_mobile_image', null, 'mobile');

    $product->setScope('ecommerce');
    echo $product->getImageHd(); // returns "my_ecommerce_image"

    $product->setScope('mobile');
    echo $product->getImageHd(); // returns "my_mobile_image"


* Manage translations + scopes

By the way, product attributes can be define as scopable AND localizable.

.. code-block:: php

    $pm = $this->productManager;
    $product = $pm->createFlexible();

    // create a localizable attribute
    $attribute = $pm->createAttribute('pim_catalog_textarea');
    $attribute->setCode('short_description');
    $attribute->setScopable(true);
    $attribute->setTranslatable(true);

    $pm->addAttributeToProduct($product, $attribute);

    $product->setShortDescription('Ecommerce and en_US', 'en_US', 'ecommerce');
    $product->setShortDescription('Mobile and en_US', 'en_US', 'mobile');
    $product->setShortDescription('Ecommerce et fr_FR', 'fr_FR', 'ecommerce');
    $product->setShortDescription('Mobile et fr_FR', 'fr_FR', 'mobile');

    $product->setLocale('en_US');
    $product->setScope('ecommerce');
    echo $product->getShortDescription(); // returns "Ecommerce and en_US"


How to define your own product manager
--------------------------------------

You can easily define your own product manager with Sf2 DIC.
You just have to extends Akeneo PIM Catalog bundle and change `pim_catalog.manager.product.class` parameter in the 
`parameters.yml` file of your bundle.

.. code-block:: yaml

    parameters:
        pim_catalog.manager.product.class: MyProject\Bundle\CatalogBundle\Manager\ProductManager

You must afterwards create a ProductManager class extending Akeneo PIM ProductManager.

