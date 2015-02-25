How to Save Products
====================

Instantiate the saver
---------------------

The product saver is a service, you can fetch it from the container.

.. code-block:: php

    $saver = $this->getContainer()->get('pim_catalog.saver.product');

Save the products
-----------------

.. code-block:: php

    $saver->save($product); //To save one product
    $saver->saveAll($products); //To save a collection of products

.. note::

   Some options can be used as second argument of the save() or saveAll(), for instance, to re-compute the completeness
