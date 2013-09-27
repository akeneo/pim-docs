How to programmatically manipulate products
===========================================

Prerequites
-----------

The Akeneo PIM project introduces a service to help you manage your product entities.
This part of the application extends the `Oro Platform`_ bundle named `FlexibleEntityBundle`_.

.. _FlexibleEntityBundle: https://magecore.atlassian.net/wiki/display/DOC/OroFlexibleEntityBundle
.. _Oro Platform: http://www.orocrm.com/oro-platform

As stated above, the product manager is a service you can get from the symfony container :

.. code-block:: php

    $this->container->get('pim_catalog.manager.product');


In the following examples, we will use $pm as the product manager object.

Create an attribute
-------------------

* Create a text attribute

.. code-block:: php

    // create an attribute
    $attribute = $pm->createAttribute('pim_catalog_text');
    $attribute->setCode('title');

    $pm->getStorageManager()->persist($attribute);
    $pm->getStorageManager()->flush();

* Create a simple select attribute

In some cases, you will want to restrain values to a list of possibilities for a product attribute.
For instance, this example creates a color attribute with a list of predefined options:

.. code-block:: php

   $att = $pm->createAttribute('pim_catalog_simpleselect');
   $att->setCode('color');
   
   $opt1 = $pm->createAttributeOption();
   $opt1->setCode('purple');
   $att->addOption($opt1);

   $opt2 = $pm->createAttributeOption();
   $opt2->setCode('yellow');
   $att->addOption($opt2);

   $opt3 = $pm->createAttributeOption();
   $opt3->setCode('blue');
   $att->addOption($opt3);

* Create a simple select attribute with translatable values

Keeping color example, the option value `purple` is "Purple" in english and "Violet" in french.

.. code-block:: php

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

* Create a localizable attribute

.. code-block:: php

    // create a localizable attribute
    $attribute = $pm->createAttribute('pim_catalog_text');
    $attribute->setCode('name');
    $attribute->setTranslatable(true);

* Create a scopable attribute

.. code-block:: php

    // create a scopable attribute
    $attribute = $pm->createAttribute('pim_catalog_text');
    $attribute->setCode('image_hd');
    $attribute->setScopable(true);

* Create a localizable AND scopable attribute

.. code-block:: php

    // create a localizable attribute
    $attribute = $pm->createAttribute('pim_catalog_textarea');
    $attribute->setCode('short_description');
    $attribute->setScopable(true);
    $attribute->setTranslatable(true);


Create a product
----------------

.. code-block:: php

    // create a product
    $product = $pm->createFlexible();

Enrich a product
----------------

* Set text value

.. code-block:: php

    $product->setSku('akeneo-001');
    $product->setTitle('My product title');

    $productValue = $product->getSku();
    $sku = (string) $product->getSku();

* Set option value

.. code-block:: php

   $product->setColor($opt1);
   echo $product->getColor(); // returns [purple]

* Set localized value

A product can have different values depending of the locale.
We considers that the locales `en_US` and `fr_FR` are already created and activated.

.. code-block:: php

    $product->setName('My name', 'en_US');
    $product->setName('Mon nom', 'fr_FR');

    echo $product->getName(); // returns "My name"

    $product->setLocale('fr_FR');
    echo $product->getName(); // returns "Mon nom"

* Set scopable value

Akeneo PIM is a multi-channels application so you can define different scopes to use.
We considers that channels `ecommerce` and `mobile` are already created.

.. code-block:: php

    $product->setImageHd('my_ecommerce_image', null, 'ecommerce');
    $product->setImageHd('my_mobile_image', null, 'mobile');

    $product->setScope('ecommerce');
    echo $product->getImageHd(); // returns "my_ecommerce_image"

    $product->setScope('mobile');
    echo $product->getImageHd(); // returns "my_mobile_image"


* Set localizable and scopable value

.. code-block:: php

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
You just have to extends Akeneo PIM Catalog bundle and change 
`pim_catalog.manager.product.class` parameter in `config.yml` file.

.. code-block:: yaml

    parameters:
        pim_catalog.manager.product.class: MyProject\Bundle\CatalogBundle\Manager\ProductManager

You must afterwards create a ProductManager class extending Akeneo PIM ProductManager.

