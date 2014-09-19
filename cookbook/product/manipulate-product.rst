How to Programmatically Manipulate Products
===========================================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your product entities.

As stated above, the product and attribute managers are services you can get from the symfony container:

.. code-block:: php

    // product manager
    $this->container->get('pim_catalog.manager.product');

    // attribute manager
    $this->container->get('pim_catalog.manager.attribute');


In the following examples, we will use ``$pm`` as the product manager object, ``$am`` - attribute manager object.

Create an Attribute
-------------------

* Create a text attribute

.. code-block:: php

    // create an attribute
    $attribute = $am->createAttribute('pim_catalog_text');
    $attribute->setCode('title');

* Create a simple select attribute

In some cases, you will want to restrain values to a list of possibilities for an attribute.
For instance, this example creates a color attribute with a list of predefined options:

.. code-block:: php

   $att = $am->createAttribute('pim_catalog_simpleselect');
   $att->setCode('color');

   $opt1 = $am->createAttributeOption();
   $opt1->setCode('purple');
   $att->addOption($opt1);

   $opt2 = $am->createAttributeOption();
   $opt2->setCode('yellow');
   $att->addOption($opt2);

   $opt3 = $am->createAttributeOption();
   $opt3->setCode('blue');
   $att->addOption($opt3);

* Create a simple select attribute with localizable values

Keeping the color example, the value of the option **purple** is "Purple" in English and "Violet" in French.

.. code-block:: php

    $opt1 = $am->createAttributeOption();
    $opt1->setCode('purple');
    $opt1->setLocalizable(true);

    $opt1EN = $am->createAttributeOptionValue();
    $opt1EN->setLocale('en_US');
    $opt1EN->setValue('Purple');
    $opt1->addOptionValue($opt1EN);

    $opt1FR = $am->createAttributeOptionValue();
    $opt1FR->setLocale('fr_FR');
    $opt1FR->setValue('Violet');
    $opt1->addOptionValue($opt1FR);

    $att->addOption($opt1);

* Create a localizable attribute

.. code-block:: php

    // create a localizable attribute
    $attribute = $am->createAttribute('pim_catalog_text');
    $attribute->setCode('name');
    $attribute->setLocalizable(true);

* Create a scopable attribute

.. code-block:: php

    // create a scopable attribute
    $attribute = $am->createAttribute('pim_catalog_text');
    $attribute->setCode('image_hd');
    $attribute->setScopable(true);

* Create a localizable AND scopable attribute

.. code-block:: php

    // create a localizable and scopable attribute
    $attribute = $am->createAttribute('pim_catalog_textarea');
    $attribute->setCode('short_description');
    $attribute->setScopable(true);
    $attribute->setLocalizable(true);


Create a Product
----------------

.. code-block:: php

    // create a product
    $product = $pm->createProduct();

Enrich a Product
----------------

* Create a new value

.. code-block:: php

    $productValue = $pm->createProductValue();
    $productValue->setAttribute($mySkuAttribute);
    $product->addValue($productValue);

* Update a text Value

.. code-block:: php

    $product->getValue('sku')->setData('akeneo-001');
    $product->getValue('title')->setData('My product title');

    $productValue = $product->getValue('sku');
    $sku = (string) $product->getValue('sku')->getData();

* Update an Option Value

.. code-block:: php

   $product->getValue('color')->setOption($opt1);
   echo $product->getValue()->getData(); // returns [purple]

* Update a Localized Value

A product can have different values depending of the locale.
With the locales **en_US** and **fr_FR** already existing:

.. code-block:: php

    $product->getValue('name', 'en_US')->setData('My name');
    $product->getValue('name', 'fr_FR')->setData('Mon nom');

    echo $product->getValue('name')->getData(); // returns "My name"

    $product->setLocale('fr_FR');
    echo $product->getValue('name')->getData(); // returns "Mon nom"

* Set Scopable Value

Akeneo PIM is a multi-channel application so you can define different scopes to use.
We the channels (scope) **ecommerce** and **mobile** already existing:

.. code-block:: php

    $product->getValue('image_hd', null, 'ecommerce')->setData('my_ecommerce_image');
    $product->getValue('image_hd', null, 'mobile')->setData('my_mobile_image');

    $product->setScope('ecommerce');

    $product->getValue('image_hd')->getData(); // returns "my_ecommerce_image"

    $product->setScope('mobile');
    $product->getValue('image_hd')->getData(); // returns "my_mobile_image"


* Set Localizable and Scopable Value

.. code-block:: php

    $product->getValue('short_description', 'en_US', 'ecommerce')->setData('Ecommerce and en_US');
    $product->getValue('short_description', 'en_US', 'mobile')->setData('Mobile and en_US');
    $product->getValue('short_description', 'fr_FR', 'ecommerce')->setData('Ecommerce et fr_FR');
    $product->getValue('short_description', 'fr_FR', 'mobile')->setData('Mobile et fr_FR');

    $product->setLocale('en_US');
    $product->setScope('ecommerce');

    echo $product->getValue('short_description'); // returns "Ecommerce and en_US"


Get a Product
-------------

.. code-block:: php

    $product = $pm->find($myProductId);


Save a Product
--------------

.. code-block:: php

    $pm->save($product);
