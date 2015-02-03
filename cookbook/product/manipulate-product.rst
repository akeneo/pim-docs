How to Create or Update a Product
=================================

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your product entities.

As stated above, the product manager is a service you can get from the Symfony container:

.. code-block:: php

    // product manager
    $this->container->get('pim_catalog.manager.product');

In the following examples, we will use ``$pm`` as the product manager object.

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
