How to Remove Products
======================

Instantiate the Remover
---------------------

The product remover is a service, you can fetch it from the container.

.. code-block:: php

    $remover = $this->getContainer()->get('pim_catalog.remover.product');

Remove the Products
-------------------

It implements ``Akeneo\Component\StorageUtils\Remover\RemoverInterface`` and ``Akeneo\Component\StorageUtils\Remover\BulkRemoverInterface`` so you can remove one or many products.

.. code-block:: php

    $remover->remove($product); // To remove one product
    $remover->removeAll($products); // To remove a collection of products
