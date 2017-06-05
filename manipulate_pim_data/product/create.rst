How to Create Products
======================

Instantiate a New ProductBuilder
--------------------------------

The product builder is available as a service, you can fetch it from the container.

.. code-block:: php

    $productBuilder = $this->getContainer()->get('pim_catalog.builder.product');

Use the ProductBuilder to Create a new Product
----------------------------------------------

.. code-block:: php

    // create a product with a sku (default identifier attribute) and a family code
    $product = $productBuilder->createProduct($identifier, $familyCode);

    // create a product with a sku (default identifier attribute) and no family
    $product = $productBuilder->createProduct($identifier);

    // create a product without identifier or family
    $product = $productBuilder->createProduct();
